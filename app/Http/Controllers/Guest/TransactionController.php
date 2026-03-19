<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailTransactionResource;
use App\ResponseData;
use App\Service\MenuService;
use App\Service\PaymentMethodService;
use App\Service\TransactionService;
use App\StatusTransaction;
use App\TransactionPaymentProofStatus;
use App\Utils\TransactionHelper;
use Exception;
use Illuminate\Http\Request;
use Log;

class TransactionController extends Controller
{

    private TransactionService $transactionService;

    private PaymentMethodService $paymentMethodeService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->paymentMethodeService = new PaymentMethodService();
        $this->responseData = new ResponseData;
    }
    public function detail(Request $request, string $id)
    {
        try {
            $transaction = $this->transactionService->detail(
                $id,
                token : $request->query('token'),
            );

            if (!$transaction) {
                return $ressponse = $this->responseData->create(
                    'Tidak dapat menemukan transaksi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('profile.order.detail', compact('response'));
            }

            $payment_methods = [];

            if (StatusTransaction::tryFrom($transaction->status) === StatusTransaction::PENDING && !$transaction->payment_proof || $transaction->payment_proof && TransactionPaymentProofStatus::tryFrom($transaction->payment_proof->status) === TransactionPaymentProofStatus::REJECTED) {
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
}
