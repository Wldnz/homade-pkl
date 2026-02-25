<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailTransactionResource;
use App\Http\Resources\TransactionResource;
use App\ResponseData;
use App\Service\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Log;

class TransactionController extends Controller
{

    private TransactionService $transactionService;
    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->responseData = new ResponseData();
    }

    public function orders(Request $request)
    {
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

            if (count($transactions) == 0) {
                $response =  $this->responseData->create(
                    'Tidak dapat menemukan transaksi atau kamu belum memiliki transaksi!',
                    status: 'warning',
                    status_code: 404,
                    isJson:false
                );
                return view('profile.orders', compact('response'));
            }

            $response = $this->responseData->create(
                'Successfully Getting Data!',
                TransactionResource::collection($transactions),
                isJson:false
            );

            return view('profile.orders', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response= $this->responseData->create(
                message: 'Kesalahan Server',
                status: 'error',
                status_code: 500,
                isJson:false
            );
            return view('profile.orders', compact('response'));
        }
    }

    public function detail(string $id)
    {
        try {
            $transaction = $this->transactionService->detail($id);

            if (!$transaction) {
                $ressponse = $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson:false
                );
                return view('profile.detail-order', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan transaksi!',
                new DetailTransactionResource($transaction),
                isJson:false
            );

            return view('profile.detail-order', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Server',
                status: 'error',
                status_code: 500,
                isJson:false
            );
            return view('profile.detail-order', compact('response'));
        }
    }

}
