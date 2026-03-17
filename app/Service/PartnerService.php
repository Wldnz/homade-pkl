<?php

namespace App\Service;

use App\Models\Partner;
use App\Utils\CloudinaryClient;
use Exception;
use Illuminate\Http\UploadedFile;
use Log;

class PartnerService
{

    private string $folder_name = 'partners';

    public function all(
        array $columns = ['name', 'name', 'image_url'],
        string|null $search = null,
        int|null $limit = 30,
        bool $is_has_limit = true,
    ) {
        $partners = Partner::when($search, function ($query, $search) {
            $search = strtolower($search);
            return $query->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
        });
        if($is_has_limit){
            return $partners->paginate($limit);
        }
        return $partners->get();
    }

    public function detail(string $id)
    {
        return Partner::where('id', $id)->first();
    }

    public function save(
        array $data,
        UploadedFile $image,
    ) {
        try {

            if (!$image) {
                return [
                    'is_success' => false,
                    'message' => 'Pastikan gambar sudah diberikan!'
                ];
            }

            $cloudinary = new CloudinaryClient();
            $uplouded = $cloudinary->uploud(
                $image->getRealPath(),
                $this->folder_name,
            );

            if (!$uplouded) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak berhasil dalam mengunggah gambar',
                ];
            }

            Partner::create([
                'name' => $data['name'],
                'image_url' => $uplouded['secure_url'],
                'image_public_id' => $uplouded['public_id'],
                'created_now' => now(),
                'updated_now' => now(),
            ]);

            return [
                'is_success' => true,
                'message' => 'Berhasil dalam membuat partner'
            ];


        } catch (Exception $e) {
            Log::error('Error When Creating: ' . $e->getMessage());
            if ($uplouded) {
                $deleted = $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah Terjadi Kesalahan Pada Saat Ingin Membuat Partner Perusahaan'
            ];
        }
    }

    public function edit(
        Partner $partner,
        array $data,
        UploadedFile|null $image,
    ) {
        try {

            $cloudinary = new CloudinaryClient();
            $old_public_id = $partner['image_public_id'];
            $uplouded = null;
            if ($image) {
                $uplouded = $cloudinary->uploud(
                    $image->getRealPath(),
                    $this->folder_name,
                );

                if (!$uplouded) {
                    return [
                        'is_success' => false,
                        'message' => 'Tidak berhasil dalam mengunggah gambar',
                    ];
                }
            }

            $partner->name = $data['name'];
            $partner->setUpdatedAt(now());


            if ($uplouded) {
                $partner->image_url = $uplouded['secure_url'];
                $partner->image_public_id = $uplouded['public_id'];
            }

            $partner->save();

            if ($uplouded) {
                $deleted = $cloudinary->delete($old_public_id);
            }

            $message = $uplouded ? 'Berhasil Dalam Merubah Data Partner & Mengubah Gambar' : 'Berhasil Dalam Merubah Data Partner';

            return [
                'is_success' => true,
                'message' => $message,
            ];


        } catch (Exception $e) {
            Log::error('Error When Creating: ' . $e->getMessage());
            if ($uplouded) {
                $deleted = $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah Terjadi Kesalahan Pada Saat Ingin Membuat Partner Perusahaan'
            ];
        }
    }
    public function delete(Partner $partner)
    {
        $public_id = $partner->image_public_id;
        $deleted = $partner->delete();
        // perlu if else?
        $cloudinary = new CloudinaryClient();
        $deleted = $cloudinary->delete($public_id);
        return $deleted;
    }


}