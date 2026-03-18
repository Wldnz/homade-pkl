<?php

namespace App\Service;

use App\Models\PaymentMethod;
use App\Utils\CloudinaryClient;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentMethodService
{
    private CloudinaryClient $cloudinaryClient;

    public function __construct()
    {
        $this->cloudinaryClient = new CloudinaryClient();
    }

    public function all(string|null $search = null, $limit = 8, bool $is_has_limit = false)
    {
        $query = PaymentMethod::query();

        if ($search) {
            $query->where('bank_name', 'LIKE', "%{$search}%")
                ->orWhere('account_owner', 'LIKE', "%{$search}%")
                ->orWhere('account_number', 'LIKE', "%{$search}%");
        }

        if ($is_has_limit) {
            return $query->paginate($limit);
        } else {
            return $query->get();
        }
    }

    public function detail($id)
    {
        return PaymentMethod::find($id);
    }

    public function save(array $data)
    {
        $uploadedImage = null;

        DB::beginTransaction();
        try {
            if (isset($data['image'])) {
                $uploadedImage = $this->cloudinaryClient->uploud($data['image'], 'payment_methods');
                if ($uploadedImage) {
                    $data['image_url'] = $uploadedImage['secure_url'];
                    $data['image_public_id'] = $uploadedImage['public_id'];
                }else{
                    return false;
                }
            }

            $data['is_active'] = isset($data['is_active']) ? true : false;

            $payment = PaymentMethod::create($data);

            DB::commit();
            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            if ($uploadedImage && isset($uploadedImage['public_id'])) {
                $this->cloudinaryClient->delete($uploadedImage['public_id']);
            }
            throw $e;
        }
    }

    public function edit(PaymentMethod $payment, array $data)
    {
        $oldPublicId = $payment->image_public_id;
        $newUploadedImage = null;

        DB::beginTransaction();
        try {
            if (isset($data['image'])) {
                $newUploadedImage = $this->cloudinaryClient->uploud($data['image'], 'payment_methods');
                if ($newUploadedImage) {
                    $data['image_url'] = $newUploadedImage['secure_url'];
                    $data['image_public_id'] = $newUploadedImage['public_id'];
                }else{
                    return false;
                }
            }

            $data['is_active'] = isset($data['is_active']) ? true : false;
            $payment->update($data);

            DB::commit();

            if ($newUploadedImage && $oldPublicId) {
                $this->cloudinaryClient->delete($oldPublicId);
            }

            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            if ($newUploadedImage && isset($newUploadedImage['public_id'])) {
                $this->cloudinaryClient->delete($newUploadedImage['public_id']);
            }
            throw $e;
        }
    }

    public function delete(PaymentMethod $payment)
    {
        $publicId = $payment->image_public_id;

        DB::beginTransaction();
        try {
            $payment->delete();
            DB::commit();

            if ($publicId) {
                $this->cloudinaryClient->delete($publicId);
            }
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}