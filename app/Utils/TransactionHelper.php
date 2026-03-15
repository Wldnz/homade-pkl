<?php

namespace App\Utils;

use App\Http\Resources\SummaryMenuResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\MenuService;
use App\Service\TransactionService;
use App\Service\UserAddressService;
use App\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Validator;

class TransactionHelper
{
    private TransactionService $transactionService;
    private UserAddressService $userAddressService;
    private MenuService $menuService;
    private ResponseData $responseData;

    /**
     * Create a new class instance.
     */

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->userAddressService = new UserAddressService();
        $this->menuService = new MenuService();
        $this->responseData = new ResponseData();
    }

    public function checkout(
        Request $request,
        bool $is_json = false,
        bool $is_pre_checkout = false,
        bool $is_created_by_customer = true,
    ) {

        $rules = [
            'items' => 'array|required',
            'items.*.id' => 'uuid|required',
            'items.*.packages' => 'array|required',
            'items.*.packages.*.id' => 'uuid|required',
            'items.*.packages.*.quantity' => 'int|required',
            'delivery_at' => ['required', Rule::date()->afterToday()]
        ];

        if (!$is_pre_checkout && $is_created_by_customer) {
            $rules['user_address_id'] = 'uuid|required';
        }

        $validator = Validator::make($request->all(), $rules, [
            'required' => 'Membutuhkan Data: :attribute!',
            'array' => ':attribute harus berupa array',
            'uuid' => ':attribute harus berupa uuid',
            'integer' => ':attribute harus berupa bilangan bulat',
            'date' => ':attribute harus berupa tanggal yang valid',
            'after' => ':attribute minimal adalah besok hari'
        ], [
            'items' => 'List Menu Yang Dipesan',
            'items.*.id' => 'ID Menu',
            'items.*.packages' => 'List Paket Menu',
            'items.*.packages.*.id' => 'ID Paket Menu',
            'items.*.packages.*.quantity' => 'Jumlah Pemesanan',
            'delivery_at' => 'Tanggal Pengiriman',
            'user_address_id' => 'Alamat Id Customer'
        ]);

        if ($validator->fails()) {
            return $this->responseData->create(
                'Data Yang Diberikan Belum Valid',
                errors: $validator->errors()->toArray(),
                status: 'warning',
                status_code: 422,
                isJson: $is_json
            );
        }

        // mendapatkan menu dari paket menu yang dipilih
        $delivery_at = Carbon::parse($request->delivery_at);
        $menus = $this->menuService->getOrderedMenu($request->items, $delivery_at);

        if ($menus->isEmpty()) {
            return $this->responseData->create(
                'Tidak dapat menemukan menu yang dipesan',
                status: 'warning',
                status_code: 404,
                isJson: $is_json
            );
        }

        // mengidentifikasi kategori
        $category = $this->getCategoryTransaction($menus);
        // melakukan pengecekan terlebuh dahulu apakah sekarang sudah di jam 3 sore atau blm
        if ($category == TransactionCategory::ORDER && !$this->canOrderAtThisTime($delivery_at)) {
            return $this->responseData->create(
                'Maaf, kami sudah menutup pemesanan untuk orderan menu mingguan pada besok hari',
                status: 'warning',
                status_code: 400,
                isJson: $is_json
            );
        }
        // melakukan pengecekan terkait setiap minimal_pemesanan
        $isPassedMiniumOrder = $this->getMinimumOrder($menus, $category, $delivery_at);
        if ($isPassedMiniumOrder['status'] !== 'success') {
            return $this->responseData->create(
                $isPassedMiniumOrder['message'],
                status: $isPassedMiniumOrder['status'],
                status_code: $isPassedMiniumOrder['status_code'],
                isJson: $is_json
            );
        }
        // membuat summary
        $address = null;

        if ($is_pre_checkout && $is_created_by_customer) {
            // jika masih di pre checkout cek harga dan dapat
            // kan data lainnya
            $address = $this->userAddressService->all();
            $address = UserAddressResource::collection($address);
        } elseif (!$is_pre_checkout && $is_created_by_customer) {
            $address = $this->userAddressService->byID($request->user_address_id);
            if (!$address) {
                return $this->responseData->create(
                    'Tidak dapat menemukan alamat cusomter',
                    status: 'warning',
                    status_code: 404,
                );
            }
        } else {
            // ini buat admin yang buat transaksi
            // bisa banget buat ambil / masukin untuk alamatnya, bagi transaksi yang tidak memiliki akun di aplikasi / website
        }

        $user = null;
        if ($is_created_by_customer) {
            $user = auth()->user();
        }

        // return $request->items;

        return $this->responseData->create(
            'Berhasil Membuatkan Data Check-Out',
            data: [
                'transaction' => [
                    'sub_total' => $this->countTotalPrice($menus),
                    'total_item' => $menus->count(),
                    'shipping_cost' => 0,
                    'category' => $category,
                    'note' => $request->note ?? ''
                ],
                'delivery_info' => [
                    'delivery_at' => $delivery_at,
                    'user_address' => $address,
                ],
                'user_info' => $user,
                'summary_orders' => [
                    'items' => SummaryMenuResource::collection($menus)->toArray($request),
                ],
            ],
            isJson: $is_json
        );
    }

    public function getCategoryTransaction($orderedMenus)
    {
        $category = TransactionCategory::ORDER;

        // kalo gw ya... kita bisa pre order menu mingguan dengan minimal 50 box dan jika dikirimkan hari minggu maka minimal 100 box;
        // jadi cara ceknya adalah, kita tentuin terlebih dahulu.. jika hanya ada semua menunya adalah mingguan maka minimal pemesanan box berdasarkan paket.

        foreach ($orderedMenus as $order) {
            if ($order->category === 'non-weekly') {
                $category = TransactionCategory::PRE_ORDER;
                break;
            }
        }

        return $category;
    }

    public function getMinimumOrder(
        $orderedMenus,
        TransactionCategory|string $category,
        Carbon $delivery_at,
    ) {
        if ($category === TransactionCategory::ORDER) {
            foreach ($orderedMenus as $order) {
                foreach ($order->prices as $price) {
                    // ini bisa bikin bingung si penggunaan '-' wkkw. intinya cek apakah quantity dibawah minimum_order
                    $minumum_order = $price->package->minimum_order;
                    if ($price->quantity < $minumum_order) {
                        return $this->responseData->create(
                            'Paket: ' . $price->package->name . ' ' . $order->name . ' Minimal Pemesanan Adalah ' . $minumum_order,
                            status: 'warning',
                            status_code: 400,
                            isJson: false
                        );
                    }
                }
            }
        } else if ($category === TransactionCategory::PRE_ORDER) {
            foreach ($orderedMenus as $order) {
                $minumum_order = $this->checkMinimumPreOrder($delivery_at);
                foreach ($order->prices as $price) {
                    // ini bisa bikin bingung si penggunaan '-' wkkw. intinya cek apakah quantity dibawah minimum_order
                    if ($price->quantity < $minumum_order) {
                        return $this->responseData->create(
                            'Paket: ' . $price->package->name . ' ' . $order->name . ' Minimal Pemesanan Adalah ' . $minumum_order,
                            status: 'warning',
                            status_code: 400,
                            isJson: false
                        );
                    }
                }
            }
        }
        return $this->responseData->create('Syarat & Ketentuan Sudah Terpenuhi', isJson: false);
    }

    public function checkMinimumPreOrder(Carbon $delivery_at)
    {
        // dilihat dari tanggal pengiriman
        // default:50 jika hari weekened itu menjadi 100;
        $minimum_order = 50;
        if ($delivery_at->isWeekend()) {
            $minimum_order = 100;
        }
        return $minimum_order;
    }

    public function canOrderAtThisTime(Carbon $delivery_at)
    {
        $today = Carbon::today()->setTime(15, 0, 0);

        if ($delivery_at->isTomorrow() && now()->greaterThan($today)) {
            return false;
        }
        return true;
    }

    public function countTotalPrice(Collection $menus)
    {
        $total_price = 0;
        foreach ($menus as $menu) {
            foreach ($menu->prices as $price) {
                $total_price += $price->price * $price->quantity;
            }
        }
        return $total_price;
    }

}
