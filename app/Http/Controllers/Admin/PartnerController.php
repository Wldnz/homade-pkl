<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\PartnerService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class PartnerController extends Controller
{
    private ResponseData $responseData;

    private PartnerService $partnerService;

    public function __construct()
    {
        $this->partnerService = new PartnerService;
        $this->responseData = new ResponseData;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $limit = $request->query('limit', 8);
            $partners = $this->partnerService->all(
                search: $search,
                limit: $limit
            );

            if ($partners->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan partner - partner perusahaan',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return view('admin.partner.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan partner - partner perusahaan',
                [
                    'pagination' => (new PaginationResource($partners))->toArray($request),
                    'partners' => $partners->toArray()['data'],
                ],
                isJson: false
            );

            return view('admin.partner.index', compact('response'));

        } catch (Exception $e) {
            Log::error('Halaman Partners Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.partner.index', compact('response'));
        }
    }

    public function detail(string $id)
    {
        try {

            $partner = $this->partnerService->detail($id);

            if (!$partner) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan partner',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.partner.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan data partner',
                $partner,
                isJson: false
            );

            return view('admin.partner.detail', compact('response'));


        } catch (Exception $e) {
            Log::error('Halaman Detail Partner Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.partner.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('admin.partner.store');
    }

    public function storeHandler(
        Request $request,
    ) {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
            ], [
                'required' => 'Pastikan :attribute Sudah Dikirimkan!',
                'image' => ':attribute harus berupa gambar'
            ], [
                'name' => 'Nama Partner',
                'image' => 'Gambar Atau Logo Partner'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid!',
                    errors: $validator->errors()->toArray(),

                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $created_info = $this->partnerService->save($request->only('name'), $request->file('image'));

            if (!$created_info['is_success']) {
                $response = $this->responseData->create(
                    $created_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $created_info['message'],
                status_code: 201,
                isJson: false
            );

            return redirect()->route('admin.partners')->with(compact('response'));

        } catch (Exception $e) {
            Log::error('Halaman Store Partner Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function editHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
            ], [
                'required' => 'Pastikan :attribute Sudah Dikirimkan!',
                'image' => ':attribute harus berupa gambar dengan dengan extension jpeg, png, jpg, webp dan maksimal 2mb'
            ], [
                'name' => 'Nama Partner',
                'image' => 'Gambar Atau Logo Partner'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid!',
                    status: 'warning',
                    errors: $validator->errors()->toArray(),
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $partner = $this->partnerService->detail($id);

            if (!$partner) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan partner',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->partnerService->edit(
                $partner,
                $request->only('name'),
                $request->file('image')
            );

            if (!$updated_info['is_success']) {
                $repsonse = $this->responseData->create(
                    $updated_info['message'],
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $updated_info['message'],
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));


        } catch (Exception $e) {
            Log::error('Halaman Edit Partner Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function deleteHandler(Request $request, string $id)
    {
        try {

            $partner = $this->partnerService->detail($id);

            if (!$partner) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan partner',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $deleted_info = $this->partnerService->delete($partner);
            $response = $this->responseData->create(
                'Berhasil dalam menghapus data',
                isJson: false
            );

            return redirect()->route('admin.partners')->with(compact('response'));

        } catch (Exception $e) {
            Log::error('Halaman Delete Partner Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

}
