<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DetailMenuResource;
use App\Http\Resources\Admin\PackageResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\ThemeResource;
use App\ResponseData;
use App\Service\CategoryService;
use App\Service\MenuService;
use App\Service\PackageService;
use App\Service\ThemeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;
use Validator;

class MenuController extends Controller
{

    private ResponseData $responseData;
    private MenuService $menuService;
    private ThemeService $themeService;
    private CategoryService $categoryService;
    private PackageService $packageService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->menuService = new MenuService();
        $this->categoryService = new categoryService();
        $this->themeService = new ThemeService();
        $this->packageService = new PackageService();
    }

    public function index(Request $request)
    {
        // buthu revisi lai si ini
        try {

            $search = $request->query('search');
            $theme = $request->query('theme');
            $category = $request->query('category');
            $limit = $request->query('limit', 8);
            $status_active = $request->query('status_active', 'all');

            $menus = $this->menuService->all(
                $search,
                $theme,
                $category,
                $limit,
                $status_active
            );

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.menu.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu',
                [
                    'pagination' => new PaginationResource($menus),
                    'menus' => MenuResource::collection($menus)->toArray($request),
                ],
                isJson: false
            );

            return view('admin.menu.index', compact('response'));

        } catch (Exception $e) {
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('admin.menu.index', compact('response'));
        }
    }

    public function detail(Request $request, string $id)
    {
        try {

            $menu = $this->menuService->searchByID($id);

            if (!$menu) {
                $response = $this->responseData->create(
                    'Tidak dapa menemukan menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.menu.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu',
                [
                    "themes" => ThemeResource::collection($this->themeService->all(is_has_limit: false))->toArray($request),
                    "categories" => CategoryResource::collection($this->categoryService->all(is_has_limit: false))->toArray($request),
                    "packages" => PackageResource::collection($this->packageService->all(is_has_limit: false))->toArray($request),
                    "detail_menu" => (new DetailMenuResource($menu))->toArray($request),
                ],
                isJson: false
            );

            return view('admin.menu.detail', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('admin.menu.detail', compact('response'));
        }
    }

    public function store(Request $request)
    {
        // butuh kirim apa aja disini?
        // paket-paket, category, theme, dah itu aja
        try {
            $response = $this->responseData->create(
                'Berhasil Mendapatkan Paket - Paket, Kategori - Kategori, Dan Tema',
                [
                    'themes' => ThemeResource::collection($this->themeService->all(is_has_limit: false))->toArray($request),
                    'packages' => PackageResource::collection($this->packageService->all(is_has_limit: false))->toArray($request),
                    'categories' => CategoryResource::collection($this->categoryService->all(is_has_limit: false))->toArray($request)
                ],
                isJson: false,
            );
            return view('admin.menu.store', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500
            );
            return view('admin.menu.store', compact('response'));
        }
    }

    public function storeHandler(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'theme_id' => 'required|uuid',
                'name' => 'required|string|min:3',
                'description' => 'required|string|min:10',
                'side_dish' => 'required|string|min:1',
                'vegetable' => 'string|min:1',
                'sauce' => '',
                'fruit' => '',
                'status_active' => ['required', Rule::in(['active', 'non-active'])],
                'category_ids' => 'array',
                'category_ids.*' => 'uuid',
                'packages' => 'required|array',
                'packages.*.package_id' => 'required|uuid',
                'packages.*.price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min!',
                'string' => ':attribute harus berupa string',
                'in' => ':attribute harus memiliki setidaknya active atau non-active',
                'mimes' => ':attribute harus berupa :mimes',
                'image' => ':attribute harus berupa gambar',
                'numeric' => ':attribute harus berupa angka numerik',
                'uuid' => ':attribute harus berupa sebuah uuid'
            ], [
                'theme_id' => 'Tema',
                'name' => 'Nama Menu',
                'description' => 'Deskripsi',
                'vegetable' => 'Sayuran',
                'sauce' => 'Jenis Saos',
                'status_active' => 'Status Aktif',
                'image' => 'Gambar Menu',
                'packages.*.package_id' => 'Id Paket Menu',
                'packages.*.price' => 'Harga Paket Menu',
                'category_ids' => 'List Kategori',
                'category_ids.*' => 'Kategori Id',
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

            $created_info = $this->menuService->save($request->all());

            if (!$created_info['is_success']) {
                $response = $this->responseData->create(
                    $$created_info['message'],
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                $created_info['message'],
                status_code: 201,
                isJson: false,
            );

            return redirect()->route('admin.menus')->with(compact('response'));


        } catch (Exception $e) {
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

            // return $request->all();

            // ada validasi jga disini
            $validator = Validator::make($request->all(), [
                'theme_id' => 'required|uuid',
                'name' => 'required|string|min:3',
                'description' => 'required|string|min:10',
                'side_dish' => 'required|string|min:1',
                'vegetable' => 'string|min:1',
                'sauce' => '',
                'fruit' => '',
                'status_active' => ['required', Rule::in(['active', 'non-active'])],
                'category_ids' => 'array',
                'category_ids.*' => 'uuid',
                'packages' => 'required|array',
                'packages.*.package_id' => 'required|uuid',
                'packages.*.price' => 'required|numeric|min:0',
                'image' => 'image|mimes:jpg,png,jpeg,webp|max:2048',
            ], [
                'required' => 'Membutuhkan :attribute',
                'min' => ':attribute membutuhkan minimal :min!',
                'string' => ':attribute harus berupa string',
                'in' => ':attribute harus memiliki setidaknya active atau non-active',
                'mimes' => ':attribute harus berupa :mimes',
                'image' => ':attribute harus berupa gambar',
                'numeric' => ':attribute harus berupa angka numerik',
                'uuid' => ':attribute harus berupa sebuah uuid'
            ], [
                'theme_id' => 'Tema',
                'name' => 'Nama Menu',
                'description' => 'Deskripsi',
                'vegetable' => 'Sayuran',
                'sauce' => 'Jenis Saos',
                'status_active' => 'Status Aktif',
                'image' => 'Gambar Menu',
                'packages.*.package_id' => 'Id Paket Menu',
                'packages.*.price' => 'Harga Paket Menu',
                'category_ids' => 'List Kategori',
                'category_ids.*' => 'Kategori Id',
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


            $menu = $this->menuService->searchByID($id);

            if (!$menu) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->menuService->edit(
                $menu,
                $request->all()
            );

            if (!$updated_info['is_success']) {
                $response = $this->responseData->create(
                    $$updated_info['message'],
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
