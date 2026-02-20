<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailTransaction;
use App\Http\Resources\TransactionResource;
use App\ResponseData;
use App\Service\TransactionService;
use Illuminate\Http\Request;
use Log;
use Exception;

class TransactionController extends Controller
{

    private TransactionService $transactionService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->responseData = new ResponseData();
    }

    public function all(Request $request)
    {

        // delivery_at
        // filter
        // status
        // status_delivery
        // search engine
        // limit

        // today, tomorrow, week, month, 6months?, a year
        $search = $request->query('search', '');
        $category = $request->query('category', '');
        $sort_by = $request->query('sort_by');
        
        $status = $request->query('status');
        $status_delivery = $request->query('status_delivery');
        
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 3);
        
        $delivery_at = $request->query('delivery_at');


        try {
            $transactions = $this->transactionService->byCustomer(
                $search,
                $category,
                $sort_by,
                $status,
                $status_delivery,
                $page,
                $limit,
                $delivery_at
            );
            
            if(count($transactions) == 0){
                return $this->responseData->create(
                    'Tidak dapat menemukan transaksi atau kamu belum memiliki transaksi!',
                    status: 'warning',
                    status_code: 404
                );
            }
            
            return $this->responseData->create(
                'Successfully Getting Data!',
                TransactionResource::collection($transactions)
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

    public function detailTransaction(string $id){
        try{
            $transaction = $this->transactionService->detail($id);;

            if(!$transaction){
                return $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status:'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil menemukan transaksi!',
                new DetailTransaction($transaction),
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Server',
                status:'error',
                status_code:500
            );
        }
    }

}
