<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\PaymentMethodService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    private ResponseData $responseData;
    private PaymentMethodService $paymentMethodService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->paymentMethodService = new PaymentMethodService();
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $limit = $request->query('limit', 8);
        $payments = $this->paymentMethodService->all($search, $limit, true);

        if ($payments->isEmpty()) {
            $response = $this->responseData->create(
                'Tidak dapat menemukan jenis pembayaran',
                status: 'warning',
                status_code: 404,
                isJson: false,
            );
            return view('admin.payment_method.index', compact('response'));
        }

        $response = $this->responseData->create(
            'Berhasil mengambil data',
            [
                'pagination' => new PaginationResource($payments),
                'payments' => $payments->toArray()['data']
            ],
            isJson: false
        );
        return view('admin.payment_method.index', compact('response'));
    }

    public function detail($id)
    {
        try {
            $payment = $this->paymentMethodService->detail($id);
            if (!$payment) {
                $response = $this->responseData->create('Data tidak ditemukan', status: 'warning', status_code: 404, isJson: false);
                return view('admin.payment_method.detail', compact('response'));
            }
            $response = $this->responseData->create('Berhasil mengambil detail', $payment, isJson: false);
            return view('admin.payment_method.detail', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create('Telah Terjadi Kesalahan Pada Server', status: 'error', status_code: 500, isJson: false);
            return view('admin.payment_method.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('admin.payment_method.store');
    }

    public function storeHandler(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'bank_name' => 'required|string|max:100',
                    'account_owner' => 'required|string|max:100',
                    'account_number' => 'required|numeric',
                    'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                ],
                [
                    'required' => ':attribute dibutuhkan!',
                    'string' => ':attribute harus berupa text',
                    'max' => ':attribute harus memiliki maksimal :max karakter',
                    'numeric' => ':attribute harus berupa angka numeric',
                    'image' => ':attribute harus berupa image dengan maksimal 2MB'
                ]
            );

            if ($validator->fails()) {
                $response = $this->responseData->create('Validasi Gagal', errors: $validator->errors()->toArray(), status: 'warning', status_code: 422, isJson: false);
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $created = $this->paymentMethodService->save($request->all());

            if (!$created) {
                $response = $this->responseData->create('Gagal Dalam Menguploud Gambar', status: 'warning', status_code: 400, isJson: false);
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create('Metode Pembayaran berhasil ditambahkan!', status_code: 201, isJson: false);
            return redirect()->route('admin.payment-methods')->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create('Terjadi kesalahan server saat menyimpan data', status: 'error', status_code: 500, isJson: false);
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function editHandler(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bank_name' => 'required|string|max:100',
                'account_owner' => 'required|string|max:100',
                'account_number' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'required' => ':attribute dibutuhkan!',
                'string' => ':attribute harus berupa text',
                'max' => ':attribute harus memiliki maksimal :max karakter',
                'numeric' => ':attribute harus berupa angka numeric',
                'image' => ':attribute harus berupa image dengan maksimal 2MB'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create('Validasi Gagal', errors: $validator->errors()->toArray(), status: 'warning', status_code: 422, isJson: false);
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $payment = $this->paymentMethodService->detail($id);
            if (!$payment) {
                $response = $this->responseData->create('Data tidak ditemukan', status: 'warning', status_code: 404, isJson: false);
                return view('admin.payment_method.detail', compact('response'));
            }

            $updated = $this->paymentMethodService->edit($payment, $request->all());

            if (!$updated) {
                $response = $this->responseData->create('Gagal Dalam Menguploud Gambar', status: 'warning', status_code: 400, isJson: false);
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create('Metode Pembayaran berhasil diupdate!', isJson: false);
            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create('Terjadi kesalahan server', status: 'error', status_code: 500, isJson: false);
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function deleteHandler($id)
    {
        try {
            $payment = $this->paymentMethodService->detail($id);
            if (!$payment) {
                $response = $this->responseData->create('Data tidak ditemukan', status: 'warning', status_code: 404, isJson: false);
                return redirect()->back()->with(compact('response'));
            }

            $response = $this->responseData->create('Metode Pembayaran berhasil dihapus!', isJson: false);
            return redirect()->route('admin.payment-methods')->with(compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create('Gagal menghapus data', status: 'error', status_code: 500, isJson: false);
            return redirect()->back()->with(compact('response'));
        }
    }
}