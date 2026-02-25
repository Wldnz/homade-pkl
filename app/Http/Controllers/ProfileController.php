<?php

namespace App\Http\Controllers;

use App\ResponseData;
use App\Service\AchievementService;
use App\Service\PartnerService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Response;

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
    
    public function profile(){

        try{

            $achievements = $this->achievementService->all();

            $partners = $this->partnerService->all();

            $response =  $this->responseData->create(
                'Succesfully getting data',
                data : [
                    'achievements' => $achievements,
                    'partners' => $partners
                ],
                isJson:false
            );

            return view('contact', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code:500,
                isJson:false
            );
            return view('contact', compact('response'));
        }

    }

}
