<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PackageResource;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\PackageService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class PackageController extends Controller
{

    private ResponseData $responseData;
    private PackageService $packageService;

    public function __construct()
    {
        $this->packageService = new PackageService();
        $this->responseData = new ResponseData();
    }

    function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $limit = $request->query('limit', 8);
            $packages = $this->packageService->all($search, $limit);

            if ($packages->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan paket - paket menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.package.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Paket - Paket Menu!',
                [
                    'pagination' => new PaginationResource($packages),
                    'packages' => PackageResource::collection($packages)
                ],
                isJson: false,
            );

            return view('admin.package.index', compact('response'));


        } catch (Exception $e) {
            Log::error('Halaman Packages Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('admin.package.index', compact('response'));
        }
    }
    function detail(Request $request, string $id)
    {
        try {

            $package = $this->packageService->detail($id);

            if (!$package) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan paket',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.package.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Paket - Paket Menu!',
                $package,
                isJson: false,
            );

            return view('admin.package.detail', compact('response'));

        } catch (Exception $e) {
            Log::error('Halaman Detail Package Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('admin.package.detail', compact('response'));
        }
    }

    function store()
    {
        return view('admin.package.store');
    }

    function storeHandler(Request $request)
    {
        try {

            $validator = Validator::make([
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
                'image' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
                'minimum_order' => 'required|int|min:1'
            ], [
                'required' => ':attribute diperlukan',
                'string' => ':attribute harus berupa string atau text',
                'image' => ':attribute harus berupa sebuah gambar',
                'integer' => ':attribute harus berupa sebuah bilangan bulat',
                'mimes' => ':attribute harus berupa format jpg,png,jpeg,webp',
                'min' => ':attribute harus memiliki minimal :min',
                'max' => ':attribute harus memiliki maximal :max'
            ], [
                'name' => 'Nam Paket',
                'description' => 'Deskripsi',
                'image' => 'Gambar Paket',
                'minimum_order' => 'Minimal Pemesanan'
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

            $created_info = $this->packageService->save(
                $request->only('name', 'description', 'minimum_order'),
                $request->file('image')
            );

            if (!$created_info['is_success']) {
                $response = $this->responseData->create(
                    $created_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $created_info['message'],
                isJson: false,
            );

            return redirect()->route('admin.packages')->with(compact('response'));


        } catch (Exception $e) {
            Log::error('Handler Membuat Package Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    function editHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make([
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
                'image' => 'image|mimes:jpg,png,jpeg,webp|max:2048',
                'minimum_order' => 'required|int|min:1'
            ], [
                'required' => ':attribute diperlukan',
                'string' => ':attribute harus berupa string atau text',
                'image' => ':attribute harus berupa sebuah gambar',
                'integer' => ':attribute harus berupa sebuah bilangan bulat',
                'mimes' => ':attribute harus berupa format jpg,png,jpeg,webp',
                'min' => ':attribute harus memiliki minimal :min',
                'max' => ':attribute harus memiliki maximal :max'
            ], [
                'name' => 'Nam Paket',
                'description' => 'Deskripsi',
                'image' => 'Gambar Paket',
                'minimum_order' => 'Minimal Pemesanan'
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

            $package = $this->packageService->detail($id);

            if (!$package) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan paket menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->packageService->edit(
                $package,
                $request->only('name', 'description', 'minimum_order'),
                $request->file('image')
            );

            if (!$updated_info['is_success']) {
                $response = $this->responseData->create(
                    $updated_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $updated_info['message'],
                isJson: false,
            );

            return redirect()->back()->with(compact('response'));


        } catch (Exception $e) {
            Log::error('Handler Merubah Package Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    function deleteHandler()
    {
    }
}
