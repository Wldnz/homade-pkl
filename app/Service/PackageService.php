<?php
namespace App\Service;
use App\Models\Package;
use App\Utils\CloudinaryClient;
use DB;
use Exception;
use Illuminate\Http\UploadedFile;
use Log;

class PackageService{

    public function all(
        string|null $search=null,
        $limit= 3,
        bool $is_has_limit = false,
    ){
        $packages = Package::when($search, function($query, $search){
            $search = strtolower($search);
            return $query->whereRAW('LOWER(name) LIKE ?', "%$search%");
        });
        if($is_has_limit){
            return $packages->paginate($limit);
        }
        return $packages->get();
    }

    public function detail(string $id){
        return Package::where('id', $id)->first();
    }

    public function save(
        array $data,
        UploadedFile|null $image
    ){

        if(!$image){
            return [
                'is_success' => false,
                'message' => 'Pastikan Gambar Paket Sudah Di Uploud!'
            ];
        }

        $cloudinary = new CloudinaryClient();
        $uplouded = $cloudinary->uploud(
            $image->getRealPath(),
            'menu_packages'
        );

        if(!$uplouded){
            return [
                'is_success' => false,
                'message' => 'Kami tidak berhasil dalam menguploud gambar'
            ];
        }

        try{
            // kenapa gw pake transaksi ya? idk lah wkwkwk
            return DB::transaction(function() use($data, $uplouded){

                Package::create([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'minimum_order' => $data['minimum_order'],
                    'image_url' => $uplouded['secure_url'],
                    'image_public_id' => $uplouded['public_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return [
                    'is_success' => true,
                    'message' => 'Berhasil membuat paket menu!',
                ];

            });

        }catch(Exception $e){
            Log::error('Error when creating package'. $e->getMessage());
            DB::rollBack();
            if($uplouded){
                $deleted = $cloudinary->delete($uplouded['public_id']);
            }
             return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan saat ingin membuat paket menu'
            ];
        }


    }
    public function edit(
        Package $package,
        array $data,
        UploadedFile|null $image
    ){
        try{

            $cloudinary = new CloudinaryClient();
            $uplouded = null;
            $old_public_id = null;
            if($image){
                $uplouded = $cloudinary->uploud(
                    $image->getRealPath(),
                    'menu_packages'
                );
                if(!$uplouded){
                    return [
                        'is_success' => false,
                        'message' => 'Tidak berhasil dalam mengunggah gambar'
                    ];
                }
                $old_public_id = $package->image_public_id;
            }

            $package->name = $data['name'];
            $package->description = $data['description'];
            $package->minimum_order = $data['minimum_order'];
            if($uplouded){
                $package->image_url = $uplouded['secure_url'];
                $package->image_public_id = $uplouded['public_id'];
            }
            $package->save();
             if($uplouded){
                $deleted = $cloudinary->delete($old_public_id);
            }

            $message = $uplouded? 'Berhasil dalam mengubah data & menguploud gambar' : 'Berhasil dalam mengubah data';

            return [
                'is_success' => true,
                'message' => $message
            ];

        }catch(Exception $e){
            Log::error('Error when editing package menu'. $e->getMessage());
             if($uplouded){
                $deleted = $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan saat ingin merubah data paket menu'
            ];
        }
    }
    public function delete() {}


}