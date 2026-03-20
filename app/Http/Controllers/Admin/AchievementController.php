<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DetailAchievement;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\AchievementService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class AchievementController extends Controller
{
    private ResponseData $responseData;
    private AchievementService $achievementService;

    public function __construct()
    {
        $this->achievementService = new AchievementService();
        $this->responseData = new ResponseData();
    }

    function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $limit = $request->query('limit', 8);

            $achievements = $this->achievementService->all(
                search: $search,
                limit: $limit,
                is_has_limit: true
            );

            if ($achievements->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan prestasi - prestasi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.achievement.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan prestasi - prestasi!',
                [
                    'pagination' => (new PaginationResource($achievements))->toArray($request),
                    'achievements' => $achievements->toArray()['data'],
                ],
                isJson: false,
            );

            return view('admin.achievement.index', compact('response'));


        } catch (Exception $e) {
            Log::error('Halaman Achivements Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('admin.achievement.index', compact('response'));
        }
    }
    function detail(Request $request, string $id)
    {
        try {

            $achievement = $this->achievementService->detail($id);

            if (!$achievement) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan prestasi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.achievement.detail', compact('response'));
            }


            $response = $this->responseData->create(
                'Berhasil Mendapatkan prestasi',
                (new DetailAchievement($achievement))->toArray($request),
                isJson: false,
            );

            return view('admin.achievement.detail', compact('response'));

        } catch (Exception $e) {
            Log::error('Halaman Detail Achievement Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('admin.achievement.detail', compact('response'));
        }
    }

    function store()
    {
        return view('admin.achievement.store');
    }

    function storeHandler(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
                'date_at' => 'date'
            ], [
                'required' => ':attribute diperlukan',
                'string' => ':attribute harus berupa string atau text',
                'date' => ':attribute harus berupa sebuah tanggal yang valid'
            ], [
                'name' => 'Nam Paket',
                'description' => 'Deskripsi',
                'date_at' => 'Tanggal Mendapatkan Prestasi'
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

            $created_info = $this->achievementService->save(
                $request->only('name', 'description', 'date_at'),
            );

           $response = $this->responseData->create(
                'Berhasil menambahkan prestasi',
                status_code:201,
                isJson: false,
            );

            return redirect()->route('admin.achievements')->with(compact('response'));


        } catch (Exception $e) {
            Log::error('Handler Membuat Achidvement Error: ' . $e->getMessage());
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

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:1',
                'description' => 'required|string|min:10',
                'date_at' => 'date'
            ], [
                'required' => ':attribute diperlukan',
                'string' => ':attribute harus berupa string atau text',
                'date' => ':attribute harus berupa sebuah tanggal yang valid'
            ], [
                'name' => 'Nam Paket',
                'description' => 'Deskripsi',
                'date_at' => 'Tanggal Mendapatkan Prestasi'
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

            $achievement = $this->achievementService->detail($id);

            if (!$achievement) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan prestasi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->achievementService->edit(
                $achievement,
                $request->only('name', 'description', 'date_at'),
            );

            $response = $this->responseData->create(
                'Berhasil memperbarui prestasi',
                isJson: false,
            );

            return redirect()->back()->with(compact('response'));


        } catch (Exception $e) {
            Log::error('Handler Merubah Achievement Error: ' . $e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    function deleteHandler(string $id)
    {
        try {

            $achievement = $this->achievementService->detail($id);

            if (!$achievement) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan prestasi',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $deleted_info = $this->achievementService->delete($achievement);

            $response = $this->responseData->create(
                'Berhasil menghapus prestasi',
                isJson: false,
            );

            return redirect()->route('admin.achievements')->with(compact('response'));

        } catch (Exception $e) {
            Log::error('Handler Menghapus Achievement Error: ' . $e->getMessage());
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
