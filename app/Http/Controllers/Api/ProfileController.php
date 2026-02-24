<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\ResponseData;
use App\Service\AchievementService;
use App\Service\PartnerService;
use Exception;
use Log;


class ProfileController extends Controller
{
    private ResponseData $responseData;
    private AchievementService $achievementService;
    private PartnerService $partnerService;

    public function __construct() {
        $this->responseData = new ResponseData();
        $this->achievementService = new AchievementService();
        $this->partnerService = new PartnerService();
    }
    
    public function achievements(){
        try{
            $achievements = $this->achievementService->all();

            if($achievements->isEmpty()){
                return $this->responseData->create(
                    'Tidak dapat menemukan prestasi',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Succesfully getting data',
                data : $achievements
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code:500,
            );
        }
    }

    public function partners(){
        try{
            // btw blm ada limit untuk saat ini, jadi semua data ke ambil
            $partners = $this->partnerService->all();

            if($partners->isEmpty()){
                return $this->responseData->create(
                    'Tidak dapat menemukan partner',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Succesfully getting data',
                 $partners
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code:500,
            );
        }

    }

}
