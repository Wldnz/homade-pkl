<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailTransactionResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\SummaryMenuResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserAddressResource;
use App\Mail\SuccessCreateTransactionEmail;
use App\ResponseData;
use App\Service\MenuService;
use App\Service\TransactionService;
use App\Service\UserAddressService;
use App\TransactionCategory;
use App\TransactionResponse;
use App\Utils\TransactionHelper;
use Carbon\Carbon;
use Date;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Log;
use Exception;
use Mail;
use Validator;

class TransactionController extends Controller
{
    private TransactionService $transactionService;
    private UserAddressService $userAddressService;
    private MenuService $menuService;
    private TransactionHelper $transactionHelper;
    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->responseData = new ResponseData();
        $this->userAddressService = new UserAddressService();
        $this->menuService = new MenuService();
        $this->transactionHelper = new TransactionHelper();
    }

    public function all(Request $request)
    {
        // today, tomorrow, week, month, 6months?, a year
        $search = $request->query('search', '');
        $category = $request->query('category', '');
        $sort_by = $request->query('sort_by');

        $status = $request->query('status');
        $status_delivery = $request->query('status_delivery');

        $limit = $request->query('limit', 3);

        $delivery_at = $request->query('delivery_at');


        try {
            $transactions = $this->transactionService->byCustomer(
                $search,
                $category,
                $sort_by,
                $status,
                $status_delivery,
                $limit,
                $delivery_at
            );

            if ($transactions->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan transaksi atau kamu belum memiliki transaksi!',
                    status: 'warning',
                    status_code: 404
                );
            }

            // $transactions = TransactionResource::collection($transactions);
            return $this->responseData->create(
                'Successfully Getting Data!',
                [
                    'pagination' => new PaginationResource($transactions),
                    'items' => TransactionResource::collection($transactions)
                ],
            );


        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                message: 'Kesalahan Server',
                status: 'error',
                status_code: 500,
            );
        }
    }
    public function detailTransaction(string $id)
    {
        try {
            $transaction = $this->transactionService->detail($id);
            ;

            if (!$transaction) {
                return $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil menemukan transaksi!',
                new DetailTransactionResource($transaction),
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Server',
                status: 'error',
                status_code: 500
            );
        }
    }

    public function checkout(Request $request)
    {
        try {
            // checkout sesuai kategori, jika kategorinya adalah pre-order maka deliver_atnya harus di anuin
            $validator = Validator::make($request->all(), [
                'items' => 'array|required',
                'items.*.id' => 'uuid|required',
                'items.*.packages' => 'array|required',
                'items.*.packages.*.id' => 'uuid|required',
                'items.*.packages.*.quantity' => 'int|required',
                'delivery_at' => ['required', Rule::date()->afterToday()]
            ], [
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
                'delivery_at' => 'Tanggal Pengiriman'
            ]);

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Data Yang Diberikan Belum Valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            // mendapatkan menu dari paket menu yang dipilih
            $delivery_at = Carbon::parse($request->delivery_at);
            $menus = $this->menuService->getOrderedMenu($request->items, $delivery_at);

            if ($menus->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu yang dipesan',
                    status: 'warning',
                    status_code: 404
                );
            }

            // mengidentifikasi kategori
            $category = $this->transactionHelper->getCategoryTransaction($menus);
            // melakukan pengecekan terlebuh dahulu apakah sekarang sudah di jam 3 sore atau blm
            if ($category == TransactionCategory::ORDER && !$this->transactionHelper->canOrderAtThisTime($delivery_at)) {
                return $this->responseData->create(
                    'Maaf, kami sudah menutup pemesanan untuk orderan menu mingguan pada besok hari',
                    status: 'warning',
                    status_code: 400
                );
            }
            // melakukan pengecekan terkait setiap minimal_pemesanan
            $isPassedMiniumOrder = $this->transactionHelper->getMinimumOrder($menus, $category, $delivery_at);
            if ($isPassedMiniumOrder['status'] !== 'success') {
                return $this->responseData->create(
                    $isPassedMiniumOrder['message'],
                    status: $isPassedMiniumOrder['status'],
                    status_code: $isPassedMiniumOrder['status_code'],
                );
            }
            // membuat summary
            $address = $this->userAddressService->all();

            // return $request->items;

            return $this->responseData->create(
                'Berhasil Membuatkan Data Check-Out',
                data: [
                    'transaction' => [
                        'sub_total' => $this->transactionHelper->countTotalPrice($menus),
                        'total_item' => $menus->count(),
                        'shipping_cost' => 0,
                        'category' => $category,
                    ],
                    'delivery_info' => [
                        'delivery_at' => $delivery_at,
                        'user_address' => UserAddressResource::collection($address),
                    ],
                    'summary_orders' => [
                        'items' => SummaryMenuResource::collection($menus),
                    ],
                ]
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }
    public function create(Request $request)
    {
        try {
            // checkout sesuai kategori, jika kategorinya adalah pre-order maka deliver_atnya harus di anuin
            $validator = Validator::make($request->all(), [
                'items' => 'array|required',
                'items.*.id' => 'uuid|required',
                'items.*.packages' => 'array|required',
                'items.*.packages.*.id' => 'uuid|required',
                'items.*.packages.*.quantity' => 'int|required',
                'user_address_id' => 'uuid|required',
                'delivery_at' => ['required', Rule::date()->afterToday()]
            ], [
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
                'delivery_at' => 'Tanggal Pengiriman'
            ]);

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Data Yang Diberikan Belum Valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            // mendapatkan menu dari paket menu yang dipilih
            $delivery_at = Carbon::parse($request->delivery_at);
            $menus = $this->menuService->getOrderedMenu($request->items, $delivery_at);

            if ($menus->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu yang dipesan',
                    status: 'warning',
                    status_code: 404
                );
            }

            // mengidentifikasi kategori
            $category = $this->transactionHelper->getCategoryTransaction($menus);
            // melakukan pengecekan terlebuh dahulu apakah sekarang sudah di jam 3 sore atau blm
            if ($category == TransactionCategory::ORDER && !$this->transactionHelper->canOrderAtThisTime($delivery_at)) {
                return $this->responseData->create(
                    'Maaf, kami sudah menutup pemesanan untuk orderan menu mingguan pada besok hari',
                    status: 'warning',
                    status_code: 400
                );
            }
            // melakukan pengecekan terkait setiap minimal_pemesanan
            $isPassedMiniumOrder = $this->transactionHelper->getMinimumOrder($menus, $category, $delivery_at);
            if ($isPassedMiniumOrder['status'] !== 'success') {
                return $this->responseData->create(
                    $isPassedMiniumOrder['message'],
                    status: $isPassedMiniumOrder['status'],
                    status_code: $isPassedMiniumOrder['status_code'],
                );
            }

            // buat transaksi disni bang

            $user = auth()->user();
            $total_price = $this->transactionHelper->countTotalPrice($menus);
            $shipping_cost = 0;
            
            $address = $this->userAddressService->byID($request->user_address_id);

            if(!$address){
                return $this->responseData->create(
                    'Tidak dapat menemukan alamat cusomter',
                    status: 'warning',
                    status_code: 404,
                );
            }

            $transaction = [
                'transaction' => [
                    'id_user' => $user->id,
                    'subtotal' => $total_price + $shipping_cost,
                    'shipping_cost' => $shipping_cost,
                    'total_price' => $total_price,
                    'category' => $category,
                    'delivery_at' => $delivery_at,
                    'note' => $request->note ?? ''
                ],
                'items' => $menus,
                'address' => $address,
            ];

            $transaction = $this->transactionService->create($transaction);

            Mail::to($user->email)->send(new SuccessCreateTransactionEmail($transaction));

            // kirim ke email admin, emailnyayang mana?

            return $this->responseData->create(
                'Berhasil membuat transaksi pemesanan',
                $transaction,
                status_code: 201,
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }


}
