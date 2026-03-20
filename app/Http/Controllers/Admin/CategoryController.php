<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\CategoryService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class CategoryController extends Controller
{
    private ResponseData $responseData;

    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService;
        $this->responseData = new ResponseData;
    }

    public function index(Request $request)
    {
        try {

            $search = $request->query('search');
            $limit = (int) $request->query('limit', 8);

            $categories = $this->categoryService->all($search, $limit);

            if ($categories->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan category',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('admin.category.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan category',
                [
                    'pagination' => (new PaginationResource($categories))->toArray($request),
                    'categories' => $categories,
                ],
                isJson: false,
            );

            return view('admin.category.index', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.category.index', compact('response'));
        }
    }

    public function detail(string $id)
    {
        try {

            $category = $this->categoryService->detail($id);

            if (!$category) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan category',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('admin.category.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan category',
                $category,
                isJson: false,
            );

            return view('admin.category.detail', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('admin.category.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('admin.category.store');
    }

    public function storeHandler(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
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

            $this->categoryService->save($request->name);

            $response = $this->responseData->create(
                'Berhasil Menambahkan data kategori',
                status_code: 201,
                isJson: false
            );

            return redirect()->route('admin.categories')->with(compact('response'));

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

            $category = $this->categoryService->detail($id);

            if (!$category) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Kategori!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->categoryService->edit($category, $request->name);

            $response = $this->responseData->create(
                'Berhasil merubah data kategori',
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
