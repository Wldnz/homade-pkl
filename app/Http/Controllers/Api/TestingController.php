<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransactionPaymentProof;
use App\ResponseData;
use App\Utils\CloudinaryClient;
use Exception;
use File;
use Illuminate\Http\Request;
use Log;
use Validator;

class TestingController extends Controller
{
    private ResponseData $responseData;

    public function __construct() {
        $this->responseData = new ResponseData();
    }

    public function uploudFile(Request $request){
        try{

            $validator = Validator::make($request->all(),[
                'uplouded_file' => 'file|required'
            ]);

            if($validator->fails()){
                return $this->responseData->create(
                    'Data yang diberikan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                );
            }

            $file = $request->file('uplouded_file');


            $uploded = (new CloudinaryClient())->uploudPaymentProof( $file->getRealPath(), fake()->uuid());

            if(!$uploded){
                return $this->responseData->create(
                    'Telah Terjadi Kesalahan Saat ingin menguploud gambar',
                    status: 'error',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan file',
                [
                    'filename' => $file->getFilename(),
                    'extension' => $file->extension(),
                    'uploded' => $uploded
                ]
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function all(){
        try{

            $images = (new CloudinaryClient())->getAllPaymentProofs();
            $images = array_filter($images, function($image) use($images){
                return $image != $images[0];
            });
            $images = array_values($images);
            return $this->responseData->create(
                'Berhasil mendapatkan file',
                [
                    'total' => count($images),
                    'images' => $images
                ]
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

     public function DeleteFile(Request $request){
        try{

            $id = $request->public_id;

            $deleted = (new CloudinaryClient())->deleteThePaymentProofImage( $id);

            return $this->responseData->create(
                'Berhasil menghapus file',
                [
                    // 'status_deleted' => $deleted['deleted'][$id],
                    'deleted' => $deleted
                ]
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return [
                'message' => $e->getMessage(),
            ];
        }
    }
}
