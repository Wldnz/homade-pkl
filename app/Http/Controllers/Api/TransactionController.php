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
use ErrorException;
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
           
            // saya buatkan function / handler pre checkout untuk memudahkan jika ada perubahan dalam satu function yaw!
            return $this->transactionHelper->checkout(
                $request,
                is_json:true,
                is_pre_checkout:true
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

            // validasi sudah ada disini semua ....
            $pre_transction_data  = $this->transactionHelper->checkout(
                $request,
            );

            if($pre_transction_data['status'] !== 'success'){
                return response()->json(
                    $pre_transction_data,
                    $pre_transction_data['status_code']
                );
            }

            $created_transaction_info = $this->transactionService->create($pre_transction_data);

            if(!$created_transaction_info['is_success']){
                throw new ErrorException($created_transaction_info['message']);
            }

            // pake que que?
            Mail::to($created_transaction_info['user']['email'])->send(new SuccessCreateTransactionEmail($created_transaction_info['transaction']));

            return $this->responseData->create(
                'Berhasil membuat transaksi pemesanan',
                $created_transaction_info['transaction'],
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
