<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\ThemeService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class ThemeController extends Controller
{
    private ResponseData $responseData;
    private ThemeService $themeService;

    public function __construct()
    {
        $this->responseData = new ResponseData;
        $this->themeService = new ThemeService();
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $limit = (int) $request->query('limit', 8);
            $themes = $this->themeService->all($search, $limit);

            if ($themes->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan tema',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('admin.theme.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan Tema',
                [
                    'pagination' => new PaginationResource($themes),
                    'themes' => $themes,
                ],
                isJson: false,
            );

            return view('admin.theme.index', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.theme.index', compact('response'));
        }
    }

    public function detail(string $id)
    {
        try {

            $theme = $this->themeService->detail($id);

            if (!$theme) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan kategori',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('admin.theme.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan tema',
                $theme,
                isJson: false,
            );

            return view('admin.theme.detail', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.theme.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('admin.theme.store');
    }

    public function storeHandler(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
            ], [
                'required' => ':attribute dibutuhkan!',
                'string' => ':attribute harus berupa string atau text ya!',
                'min' => ':attribute harus memiliki minimal :min karakter ya',
            ], [
                'name' => 'Nama Kategori',
                'description' => 'Deskripsi'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->themeService->save($request->only('name', 'description'));

            $response = $this->responseData->create(
                'Berhasil Menambahkan data tema',
                status_code:201,
                isJson: false
            );

            return redirect()->route('admin.themes')->with(compact('response'));

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

    public function editHandler(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
            ], [
                'required' => ':attribute dibutuhkan!',
                'string' => ':attribute harus berupa string atau text ya!',
                'min' => ':attribute harus memiliki minimal :min karakter ya',
            ], [
                'name' => 'Nama Kategori',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $theme = $this->themeService->detail($id);

            if (!$theme) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Tema!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->themeService->edit($theme, $request->only('name', 'description'));

            $response = $this->responseData->create(
                'Berhasil merubah data tema',
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $resposne = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->withInput()->with(compact('response'));
        }
    }
}
