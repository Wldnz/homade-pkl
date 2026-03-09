<?php

namespace App\Utils;

use Cloudinary\Cloudinary;
use Config;
use Exception;
use File;
use Log;

class CloudinaryClient
{

    private Cloudinary $cloudinary;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->cloudinary = new Cloudinary(Config::get('app.cloudinary_url'));
    }

    public function get(string $public_id)
    {
        return $this->cloudinary->image($public_id);
    }

    public function uploudPaymentProof(
        File|string $image,
    ) {
        try {
            $public_id = 'payment_proof_' . time();
            $uploud = $this->cloudinary->uploadApi()->upload($image, [
                'public_id' => $public_id,
                'use_filename' => false,
                // blm di format ke webp...
                // sementara foldernya maish berantakan lah ya hehehe...
                'folder' => 'homade/payment_proofs'
            ]);
            return $uploud;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function deleteThePaymentProofImage(
        string $public_id
    ) {
        try{
            $response = $this->cloudinary->adminApi()->deleteAssets($public_id);
            return $response['deleted'][$public_id] === 'deleted';
        }catch(Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAllPaymentProofs($limit = 2){
        try{
            $response = $this->cloudinary->adminApi()->assetsByAssetFolder('homade/payment_proofs', [
                'max_results' => $limit,
            ]);
            return $response['resources'];
        }catch(Exception $e){
             Log::error($e->getMessage());
            return [];
        }
    }

}
