<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaginationResource;
use App\Http\Resources\PartnerResources;
use App\ResponseData;
use App\Service\AchievementService;
use App\Service\PartnerService;
use Exception;
use Illuminate\Http\Request;
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
    
    public function profile(Request $request){
        try{
            $achievements = $this->achievementService->all();

            $limit_partner = $request->query('limit_partner', 15);
            $partners = $this->partnerService->all(limit: $limit_partner);

            $response =  $this->responseData->create(
                'Succesfully getting data',
                data : [
                    'achievements' => $achievements,
                    'partners' => [
                        'pagination' => (new PaginationResource($partners))->toArray($request),
                        'items' => PartnerResources::collection($partners)->toArray($request)
                    ]
                ],
                isJson:false
            );

            return view('profile', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code:500,
                isJson:false
            );
            return view('profile', compact('response'));
        }

    }

}
