<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailMenuResource;
use App\Http\Resources\DetailUserAddressResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\UserAddressService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;
use function PHPUnit\Framework\isJson;


class UserAddressController extends Controller
{
    private ResponseData $responseData;
    private UserAddressService $userAddressService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userAddressService = new UserAddressService();
    }

    public function address()
    {
        try {
            $address = $this->userAddressService->all();

            if ($address->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan alamat pengguna',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return view('profile.address', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan alamat pengguna',
                UserAddressResource::collection($address),
                isJson: false
            );

            return view('profile.address', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('profile.address', compact('response'));
        }
    }

    public function detail(string $id){
        try{
            
            $address = $this->userAddressService->byID($id);

            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->route('user.user-address')->with(compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Menemukan Detail Alamat Email',
                [
                    'target_address_id' => $address->id,
                    'form_action' => 'show',
                    'show_form' => true,
                    'address' => new DetailMenuResource($address)
                ],
                isJson: false
            );

            return redirect()->route('user.user-address')->with(compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson:false
            );
            return redirect()->route('user.user-address')->with(compact('response'));
        }
    }


    public function store(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'fullname' => 'string|required|min:3',
                'phone' => 'string|required|min:8',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'string|required|min:8',
                'latitude' => 'string|required|min:8',
            ]);

            if ($validate->fails()) {
                $response = $this->responseData->create(
                    'Data Belum Valid',
                    [
                        'errors' => $validate->errors(),
                        'show_form' => true,
                        'form_action' => 'store' 
                    ],
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            // cek terlebih dahulu apakah alamatnya sudah ada 3?
            $address = $this->userAddressService->all();

            if ($address->count() >= 3) {
                $response = $this->responseData->create(
                    'Maaf, Tidak Bisa Membuat Alamat Pengiriman Lebih Dari 3',
                    status: 'warning',
                    status_code: 403,
                    isJson: false,
                );
                return redirect()->back()->with(compact('response'));
            }

            $address = $this->userAddressService->save([
                'id_user' => auth()->user()->id,
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]);

           $response =  $this->responseData->create(
                'Berhasil Membuat Alamat Email',
                new UserAddressResource($address),
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

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

    public function edit(Request $request, string $id)
    {
        try {

            $validate = Validator::make($request->all(), [
                'fullname' => 'string|required|min:3',
                'phone' => 'string|required|min:8',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'string|required|min:8',
                'latitude' => 'string|required|min:8',
            ]);

            if ($validate->fails()) {
                $response = $this->responseData->create(
                    'Data Belum Valid',
                    [
                        'errors' => $validate->errors(),
                        'target_address_id' => $id,
                        'form_action' => 'edit' ,
                        'show_form' => true,
                    ],
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()
                    ->withInput()
                    ->with(compact('response'));
            }

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->userAddressService->update($address, [
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]);

            // langsung success aja dlu, apa perlu validasi lagi?
            $response = $this->responseData->create(
                'Berhasil mengubah data alamat email',
                new DetailUserAddressResource($address),
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

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
    public function remove(string $id)
    {
        try {

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->with(compact('response'));
            }

            $address->delete();

            $response =  $this->responseData->create(
                'Berhasil Menghapus Data',
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response =  $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return redirect()->back()->with(compact('response'));
        }
    }

}
