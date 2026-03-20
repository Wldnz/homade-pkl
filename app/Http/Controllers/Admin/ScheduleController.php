<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuScheduleResource;
use App\ResponseData;
use App\Service\MenuService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Response;
use Validator;

class ScheduleController extends Controller
{

    private ResponseData $responseData;
    private MenuService $menuService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->menuService = new MenuService();
    }

    public function index(Request $request)
    {
        try {
            $currentDate = now()->setTime(0, 0, 0);
            $week = (int) $request->query('week', $currentDate->weekOfMonth);

            $currentDate->weekOfMonth($week);
            $currentDayOfWeek = $currentDate->dayOfWeekIso; //4
            $startOfWeek = $currentDate->subDays($currentDayOfWeek - 1);
            $endOfWeek = $startOfWeek->clone()->addDays(4);
            $currentDateFormatIndonesian = $currentDate->format('d-m-Y');

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Data',
                [
                    'start_of_week' => $startOfWeek,
                    'end_of_week' => $endOfWeek,
                    'current_week' => $currentDate->weekOfMonth,
                    'current_month' => $currentDate->monthName,
                    'current_date' => $currentDate->format('d-m-Y'),
                    'menus' => MenuResource::collection($this->menuService->all(is_has_limit: false))->toArray($request),
                    'schedules' => MenuScheduleResource::collection($this->menuService->getByMultipleDay([$startOfWeek, $endOfWeek]))->toArray($request)
                ],
                isJson: false,
            );

            return view('admin.schedule.index', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return view('admin.schedule.index', compact('response'));
        }
    }

    public function storeOrUpdateHandler(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'start_of_week' => 'required|date',
                'end_of_week' => 'required|date',
                'schedules' => 'array',
                'schedules.*' => 'required|array',
                'schedules.*.*' => 'required|uuid'
            ], [
                'required' => 'diperlukan',
                'array' => ':attribute harus berupa array!',
                'uuid' => ':attriute harus berupa sebuah uuid',
                'date' => ':attriute harus berupa sebuah tanggal',
            ], [
                'schedules' => 'List Jadwal',
                'schedules.*' => 'List Menu',
                'schedules.*.*' => 'Menu'
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang diberkan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $updated_info = $this->menuService->saveWeeklyMenu(
                $request->start_of_week,
                $request->end_of_week,
                $request->schedules,
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
