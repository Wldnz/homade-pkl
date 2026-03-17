<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailTransactionResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\TransactionResource;
use App\Mail\SuccessCreateTransactionEmail;
use App\ResponseData;
use App\Service\MenuService;
use App\Service\PaymentMethodService;
use App\Service\TransactionService;
use App\Service\UserAddressService;
use App\StatusTransaction;
use App\TransactionPaymentProofStatus;
use App\Utils\TransactionHelper;
use Carbon\Carbon;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Validator;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    private TransactionHelper $transactionHelper;

    private MenuService $menuService;

    private UserAddressService $userAddressService;
    private PaymentMethodService $paymentMethodeService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService;
        $this->transactionHelper = new TransactionHelper;
        $this->menuService = new MenuService;
        $this->userAddressService = new UserAddressService;
        $this->paymentMethodeService = new PaymentMethodService();
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
                    'pagination' => (new PaginationResource($transactions))->toArray($request),
                    'orders' => TransactionResource::collection($transactions)->toArray($request),
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

            $payment_methods = [];

            if(StatusTransaction::tryFrom($transaction->status) === StatusTransaction::PENDING && !$transaction->payment_proof || $transaction->payment_proof && TransactionPaymentProofStatus::tryFrom($transaction->payment_proof->status) === TransactionPaymentProofStatus::REJECTED){
                $payment_methods = $this->paymentMethodeService->all();
            } 

            $response = $this->responseData->create(
                'Berhasil menemukan transaksi!',
                [
                    'transaction' => (new DetailTransactionResource($transaction))->toArray($request),
                    'payment_methods' => $payment_methods,
                ],
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
        $response = session()->get('session_pre_check_out_summary_data');
        if (!$response || $response['status'] !== 'success') {
            $response = $response? $response : $this->responseData->create(
                'Pastikan kamu sudah memilih menu yang ingin dipesan ya',
                status: 'warning',
                status_code: 400,
                isJson:false,
            );
            return redirect()->route('user.schedules')->with(compact('response'));
        }
        // return $response;
        return view('order.checkout', compact('response'));
    }

    public function preCheckoutHandler(Request $request)
    {
        try {
            // saya buatkan function / handler pre checkout untuk memudahkan jika ada perubahan dalam satu function yaw!
            $response = $this->transactionHelper->checkout(
                $request,
                is_pre_checkout: true
            );

            if ($response['status'] !== 'success') {
                return redirect()->back()->withInput()->with(compact('response'));
            }

            session()->put(
                'session_pre_check_out_summary_data',
                $response
            );

            return redirect()->route('user.checkout-page');

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson:false
            );
            return redirect()->back()->withInput()->with(compact('response'));
        } finally {
            if ($response['status'] !== 'success') {
                session()->forget('session_pre_check_out_summary_data');
            }
        }
    }

    public function createTransaction(Request $request)
    {
        try {

            $response = $this->transactionHelper->checkout(
                $request,
            );

            if ($response['status'] !== 'success') {
                return redirect()->back()->withInput()->with(compact('response'));
            }

            // create transaction disini?
            $created_transaction_info = $this->transactionService->create($response);

            if (!$created_transaction_info['is_success']) {
                throw new ErrorException($created_transaction_info['message']);
            }

            $response = $this->responseData->create(
                'Berhasil membuat transaksi pemesanan',
                $created_transaction_info['transaction'],
                status_code: 201,
                isJson: false
            );

            // hapus data checkout_disini..
            session()->forget('session_pre_check_out_summary_data');
            session()->put('session_after_transaction_result', $response);
            Mail::to($created_transaction_info['user']['email'])->send(new SuccessCreateTransactionEmail($created_transaction_info['transaction']));
            // redirect ke transaction berhasil di buat apa ke order transaction?
            return redirect()->route('user.after-transaction');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function afterTransactionHandler()
    {
        $response = session()->get('session_after_transaction_result');

        if (!$response) {
            return redirect()->route('user.orders');
        }

        return view('order.after-transaction', compact('response'));

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
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Dalam Membtalakn Transaksi',
                isJson: false,
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
