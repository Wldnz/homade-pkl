<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailTransactionResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\SummaryMenuResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\MenuService;
use App\Service\TransactionService;
use App\Service\UserAddressService;
use App\TransactionCategory;
use App\Utils\TransactionHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;
use Validator;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    private TransactionHelper $transactionHelper;

    private MenuService $menuService;

    private UserAddressService $userAddressService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService;
        $this->transactionHelper = new TransactionHelper;
        $this->menuService = new MenuService;
        $this->userAddressService = new UserAddressService;
        $this->responseData = new ResponseData;
    }

    public function orders(Request $request)
    {
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
                $response = $this->responseData->create(
                    'Tidak dapat menemukan transaksi atau kamu belum memiliki transaksi!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('profile.order.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Successfully Getting Data!',
                [
                    'pagination' => new PaginationResource($transactions),
                    'orders' => TransactionResource::collection($transactions),
                ],
                isJson: false
            );

            return view('profile.order.index', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                message: 'Kesalahan Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('profile.order.index', compact('response'));
        }
    }

    public function detail(Request $request, string $id)
    {
        try {
            $transaction = $this->transactionService->detail($id);

            if (!$transaction) {
                $ressponse = $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('profile.order.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan transaksi!',
                (new DetailTransactionResource($transaction))->toArray($request),
                isJson: false
            );

            return view('profile.order.detail', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('profile.order.detail', compact('response'));
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
                'delivery_at' => ['required', Rule::date()->afterToday()],
            ], [
                'required' => 'Membutuhkan Data: :attribute!',
                'array' => ':attribute harus berupa array',
                'uuid' => ':attribute harus berupa uuid',
                'integer' => ':attribute harus berupa bilangan bulat',
                'date' => ':attribute harus berupa tanggal yang valid',
                'after' => ':attribute minimal adalah besok hari',
            ], [
                'items' => 'List Menu Yang Dipesan',
                'items.*.id' => 'ID Menu',
                'items.*.packages' => 'List Paket Menu',
                'items.*.packages.*.id' => 'ID Paket Menu',
                'items.*.packages.*.quantity' => 'Jumlah Pemesanan',
                'delivery_at' => 'Tanggal Pengiriman',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data Yang Diberikan Belum Valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );

                return view('order.checkout', compact('response'));
            }

            // mendapatkan menu dari paket menu yang dipilih
            $delivery_at = Carbon::parse($request->delivery_at);
            $menus = $this->menuService->getOrderedMenu($request->items, $delivery_at);

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu yang dipesan',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('order.checkout', compact('response'));
            }

            // mengidentifikasi kategori
            $category = $this->transactionHelper->getCategoryTransaction($menus);
            // melakukan pengecekan terlebuh dahulu apakah sekarang sudah di jam 3 sore atau blm
            if ($category == TransactionCategory::ORDER && !$this->transactionHelper->canOrderAtThisTime($delivery_at)) {
                $response = $this->responseData->create(
                    'Maaf, kami sudah menutup pemesanan untuk orderan menu mingguan pada besok hari',
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );

                return view('order.checkout', compact('response'));
            }
            // melakukan pengecekan terkait setiap minimal_pemesanan
            $isPassedMiniumOrder = $this->transactionHelper->getMinimumOrder($menus, $category, $delivery_at);
            if ($isPassedMiniumOrder['status'] !== 'success') {
                $response = $this->responseData->create(
                    $isPassedMiniumOrder['message'],
                    status: $isPassedMiniumOrder['status'],
                    status_code: $isPassedMiniumOrder['status_code'],
                    isJson: false,
                );

                return view('order.checkout', compact('response'));
            }
            // membuat summary
            $address = $this->userAddressService->all();

            // return $request->items;

            $user = auth()->user();

            $response = $this->responseData->create(
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
                ],
                isJson: false
            );

            return view('order.checkout', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );

            return view('order.checkout', compact('response'));
        }
    }

    public function uploudPaymentProofHandler(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'uplouded_file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min karakter!',
                'string' => ':attribute harus berupa string',
                'image' => ':attribute harus berupa gambar',
            ], [
                'uplouded_file' => 'Buki Pembayaran',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $transaction = $this->transactionService->detail($id);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            // uploud ulang / buat ulang payment transaction!;
            $uploud_info = $this->transactionService->uploudPaymentProof($transaction, $request->file('uplouded_file'));
            if (!$uploud_info['is_success']) {
                $response = $this->responseData->create(
                    $uploud_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $uploud_info['message'],
                status: 'success',
                status_code: $uploud_info['is_created'] ? 201 : 200,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function cancelledTransactionHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'reason' => 'string|required|min:10',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min karakter!',
                'string' => ':attribute harus berupa string',
            ], [
                'reason' => 'Alasan'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $transaction = $this->transactionService->detail($id);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan transaksi!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $rejected_info = $this->transactionService->rejectTransaction($transaction, $request->reason, false);

            if (!$rejected_info['is_success']) {
                $response = $this->responseData->create(
                    $rejected_info['message'],
                    status: 'error',
                    status_code: 400,
                    isJson:false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Dalam Membtalakn Transaksi',
                isJson:false,
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }
}
