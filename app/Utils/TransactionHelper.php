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

    public function convertPayloadDataIntoArray(
        Request $request,
        bool $is_json = false,
    ) {
        $validator = Validator::make($request->all(), [
            'checkout_payload' => 'required|json'
        ], [
            'checkout_payload.json' => ':attribute harus berupa json'
        ], [
            'checkout_payload' => 'Payload Checkout'
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

        return $this->responseData->create(
            '',
            json_decode($request->checkout_payload, true),
            isJson: $is_json
        );

    }

    public function checkout(
        Request $request,
        bool $is_json = false,
        bool $is_pre_checkout = false,
        bool $is_created_by_customer = true,
        bool $is_from_website = true,
    ) {

        if ($is_from_website) {
            $response = $this->convertPayloadDataIntoArray($request, $is_json);
            if ($response['status'] !== 'success') {
                return redirect()->back()->withInput()->with(compact('response'));
            }
            // Pastikan merge hasil array-nya ke request
            $request->merge($response['data']);
        }

        // 1. RULES DASAR (Berlaku untuk Pre-Checkout & Checkout)
        $rules = [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|uuid',
            'items.*.packages' => 'required|array|min:1',
            'items.*.packages.*.id' => 'required|uuid',
            'items.*.packages.*.quantity' => 'required|integer|min:1',

            'delivery_info' => 'required|array',
            'delivery_info.delivery_at' => ['required', 'date', 'after:today'],
        ];

        // 2. RULES TAMBAHAN KHUSUS CHECKOUT
        if (!$is_pre_checkout) {
            // Validasi User Info
            $rules['user_info'] = 'required|array';
            $rules['user_info.first_name'] = 'required|string';
            $rules['user_info.last_name'] = 'required|string';
            $rules['user_info.phone'] = 'required|string|min:8';

            $rules['note'] = 'nullable|string';

            // Validasi Alamat (Hanya untuk Customer)
            if ($is_created_by_customer) {
                // Logika XOR: Pilih salah satu antara ID lama ATAU bikin alamat baru
                $rules['delivery_info.user_address_id'] = 'nullable|uuid';

                // Jika user_address_id kosong, new_user_address WAJIB ADA berupa array
                $rules['delivery_info.new_user_address'] = 'required_without:delivery_info.user_address_id|array';

                // Field di dalam alamat baru wajib diisi JIKA user_address_id kosong
                $rules['delivery_info.new_user_address.fullname'] = 'required_without:delivery_info.user_address_id|string';
                $rules['delivery_info.new_user_address.phone'] = 'required_without:delivery_info.user_address_id|string|min:8';
                $rules['delivery_info.new_user_address.label'] = 'required_without:delivery_info.user_address_id|string|min:3';
                $rules['delivery_info.new_user_address.address'] = 'required_without:delivery_info.user_address_id|string|min:8';

                $rules['delivery_info.new_user_address.note'] = 'nullable|string';
                $rules['delivery_info.new_user_address.longitude'] = 'required_without:delivery_info.user_address_id|numeric|between:-180,180';
                $rules['delivery_info.new_user_address.latitude'] = 'required_without:delivery_info.user_address_id|numeric|between:-90,90';

                $rules['delivery_info.new_user_address.is_main_address'] = 'nullable|boolean';
                $rules['delivery_info.new_user_address.save_to_profile'] = 'nullable|boolean';
            }
        }

        // 3. JALANKAN VALIDATOR
        $validator = Validator::make($request->all(), $rules, [
            'required' => 'Membutuhkan Data: :attribute!',
            'required_without' => ':attribute wajib diisi jika Anda tidak memilih Alamat Tersedia.',
            'array' => ':attribute harus berupa array',
            'uuid' => ':attribute harus berupa uuid',
            'integer' => ':attribute harus berupa bilangan bulat',
            'numeric' => ':attribute harus berupa angka',
            'date' => ':attribute harus berupa tanggal yang valid',
            'after' => ':attribute minimal adalah besok hari',
            'between' => 'Koordinat :attribute tidak valid',
            'min' => ':attribute minimal :min karakter/angka',
        ], [
            'items' => 'List Menu Yang Dipesan',
            'items.*.id' => 'ID Menu',
            'items.*.packages' => 'List Paket Menu',
            'items.*.packages.*.id' => 'ID Paket Menu',
            'items.*.packages.*.quantity' => 'Jumlah Pemesanan',

            'delivery_info.delivery_at' => 'Tanggal Pengiriman',
            'delivery_info.user_address_id' => 'Alamat Customer',

            'delivery_info.new_user_address.fullname' => 'Nama Penerima',
            'delivery_info.new_user_address.phone' => 'Nomor HP Penerima',
            'delivery_info.new_user_address.label' => 'Label Alamat',
            'delivery_info.new_user_address.address' => 'Detail Alamat',
            'delivery_info.new_user_address.longitude' => 'Longitude',
            'delivery_info.new_user_address.latitude' => 'Latitude',

            'user_info.first_name' => 'Nama Depan Pemesan',
            'user_info.last_name' => 'Nama Belakang Pemesan',
            'user_info.phone' => 'Nomor HP Pemesan',
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
        $delivery_at = Carbon::parse($request->delivery_info['delivery_at']);
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
            $user_address_id = $request->delivery_info['user_address_id'] ?? null;
            if ($user_address_id) {
                // kalo pake user id
                $address = $this->userAddressService->byID($user_address_id);
                if (!$address) {
                    return $this->responseData->create(
                        'Tidak dapat menemukan alamat cusomter',
                        status: 'warning',
                        status_code: 404,
                    );
                }
            } else {
                // kalo pake adress baru
                $address = $request->delivery_info['new_user_address'];
            }


        } else {
            // ini buat admin yang buat transaksi
            // bisa banget buat ambil / masukin untuk alamatnya, bagi transaksi yang tidak memiliki akun di aplikasi / website
        }

        // berikan user terkait sesuai apa yang diminta ajah lah... 
        $user = null;
        $is_changed = false;
        if ($is_created_by_customer) {
            $user = auth()->user();
            // ubah beberapa data jika perlu            
            if (!$is_pre_checkout && $user->phone != $request->user_info['phone']) {
                $user['phone'] = $request['user_info']['phone'];
                $is_changed = true;
            }
        } else {
            // disini ketika dibuat transaksinya sama admin
        }

        // return $request->items;

        $data = [
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
            'is_changed' => $is_changed,
        ];

        return $this->responseData->create(
            'Berhasil Membuatkan Data Check-Out',
            $data,
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
