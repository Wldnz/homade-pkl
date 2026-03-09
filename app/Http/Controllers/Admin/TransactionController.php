<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailTransactionResource;
use App\Http\Resources\PaginationResource;
use App\Mail\RejectedTransaction;
use App\Mail\SuccessfullyCreatedNewInvoice;
use App\ResponseData;
use App\Service\TransactionService;
use App\StatusDelivery;
use App\Utils\CalculateDistance;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Validator;

class TransactionController extends Controller
{
    private ResponseData $responseData;

    private TransactionService $transactionService;

    public function __construct()
    {
        $this->responseData = new ResponseData;
        $this->transactionService = new TransactionService;
    }

    public function index(Request $request)
    {
        try {

            $search = $request->query('search');
            $category = $request->query('category');
            $status = $request->query('status');
            $status_delivery = $request->query('status_delivery');
            $limit = $request->query('limit', 8);
            $delivery_at = $request->query('delivery_at');

            $transactions = $this->transactionService->all(
                search: $search,
                category: $category,
                status: $status,
                status_delivery: $status_delivery,
                limit: $limit,
                delivery_at: $delivery_at
            );

            if ($transactions->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('admin.order.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Transaksi',
                [
                    'pagination' => new PaginationResource($transactions),
                    'orders' => $transactions,
                ],
                isJson: false
            );

            return view('admin.order.index', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.order.index', compact('response'));
        }
    }

    public function detail(Request $request, string $id)
    {
        try {
            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404
                );

                return view('admin.order.detail', compact('response'));
            }

            $calculateDistance = (new CalculateDistance)->calculate($transaction->address->latitude, $transaction->address->longitude);
            $distance = 0;
            if ($calculateDistance['is_success']) {
                $distance = $calculateDistance['distance'];
            }

            $transaction->distance = $distance;

            $transaction->needed_status_information = true;

            $response = $this->responseData->create(
                'Berhasil Mengambil Data Pemesanan',
                (new DetailTransactionResource($transaction))->toArray($request),
                isJson: false,
            );

            return view('admin.order.detail', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.order.detail', compact('response'));
        }
    }

    public function changeShippingCostHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'shipping_cost' => 'numeric|required|min:0',
            ], [
                'required' => 'Pastikan mengirimakn :attribute ya!',
                'number' => ':attribute harus berupa angka ya',
                'min' => 'Minimal :attribute harus :min',
            ], [
                'shipping_cost' => 'Ongkos Kirim',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid nih!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan transaksi!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->transactionService->changeShippingCost($transaction, $request->shipping_cost);

            if (!$updated_info['is_success']) {
                $response = $this->responseData->create(
                    $updated_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $updated_info['message'],
                isJson: false
            );

            Mail::to($transaction->user->email)->send(new SuccessfullyCreatedNewInvoice($transaction));

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah terjadi kesalahan padan server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function rejectTheTransactionHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'reason' => 'string|required|min:10',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min karakter!',
                'string' => ':attribute harus berupa string',
            ], [
                'reason' => 'Alasan',
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

            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $rejected_info = $this->transactionService->rejectTransaction($transaction, $request->reason);

            if (!$rejected_info['is_success']) {
                $response = $this->responseData->create(
                    $rejected_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $rejected_info['message'],
                isJson: false,
            );

            Mail::to($transaction->user->email)->send(new RejectedTransaction($transaction));

            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'warning',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

     public function uploudThePaymentProofHandler(Request $request, string $id)
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
                'uplouded_file' => 'Bukti Pembayaran',
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

            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $uplouded_info = $this->transactionService->uploudPaymentProof(
                $transaction,
                $request->file('uplouded_file')
            );

            if (!$uplouded_info['is_success']) {
                $response = $this->responseData->create(
                    $uplouded_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $uplouded_info['message'],
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function acceptThePaymentProof(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'string|required|min:10',
                // max 2mb ini
                'uplouded_file' => 'image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min karakter!',
                'string' => ':attribute harus berupa string',
                'image' => ':attribute harus berupa gambar',
            ], [
                'uplouded_file' => 'File',
                'reason' => 'Alasan',
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

            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            if (!$transaction->payment_proof) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Bukti Pembayaran',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $accepted_info = $this->transactionService->acceptThePaymentProof(
                $transaction,
                $request->reason,
                $request->file('uplouded_file')
            );

            if (!$accepted_info['is_success']) {
                $response = $this->responseData->create(
                    $accepted_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $accepted_info['message'],
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function rejectThePaymentProof(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'string|required|min:10',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min karakter!',
                'string' => ':attribute harus berupa string',
            ], [
                'reason' => 'Alasan',
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

            $transaction = $this->transactionService->detail($id, false);

            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            if (!$transaction->payment_proof) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Bukti Pembayaran',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $rejected_info = $this->transactionService->rejectThePaymentProof($transaction, $request->reason);

            if (!$rejected_info['is_success']) {
                $response = $this->responseData->create(
                    $rejected_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $rejected_info['message'],
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function refundTheTransactionHandler()
    {
    }

    public function changeDeliveryStatusHandler(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'delivery_status' => 'required|string',
            ], [
                'required' => ':attribute diperlukan',
                'string' => ':atrribute harus berupa string',
            ], [
                'delivery_status' => 'Status Pengiriman',
            ]);
            $transaction = $this->transactionService->detail($id, false);
            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->with(compact('response'));
            }

            $changedStatusInfo = $this->transactionService->changeStatusDelivery($transaction, $request->delivery_status);

            if (!$changedStatusInfo['is_success']) {
                $response = $this->responseData->create(
                    $changedStatusInfo['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $changedStatusInfo['message'],
                isJson: false
            );

            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->with(compact('response'));
        }
    }

    public function completeTheTransactionHandler(string $id)
    {
        try {

            $transaction = $this->transactionService->detail($id, false);
            if (!$transaction) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->with(compact('response'));
            }

            // sbnrnya masih harus validasi lagi ke delivery status, payment_proof status, refund status dah gitu
            $completed_info = $this->transactionService->completeTheTransaction($transaction);

            if (!$completed_info['is_success']) {
                $response = $this->responseData->create(
                    $completed_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $completed_info['message'],
                isJson: false
            );

            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->with(compact('response'));
        }
    }

    public function store()
    {
        return view('admin.order.store');
    }
}
