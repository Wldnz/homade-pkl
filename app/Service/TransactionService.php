<?php

namespace App\Service;

use App\Mail\SuccessCreateTransactionEmail;
use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\TransactionOrder;
use App\Models\TransactionPaymentProof;
use App\RefundStatus;
use App\StatusDelivery;
use App\StatusTransaction;
use App\TransactionPaymentProofStatus;
use App\Utils\CloudinaryClient;
use Carbon\Carbon;
use Cloudinary\Cloudinary;
use ErrorException;
use Exception;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;
use PHPUnit\TextUI\XmlConfiguration\FailedSchemaDetectionResult;
use Str;

class TransactionService
{

    // note buat sekarang... semua transaksi minimal mengambil satu gambar menu makanan dari order yng sudha ada

    public function all(
        string|null $search,
        string|null $category,
        string|null $status,
        string|null $status_delivery,
        int $limit = 8,
        string|null $delivery_at
    ) {
        return Transaction::with('user')
            ->when($search, function ($query, $search) {
                return $query->whereHas('orders.menu_price.menu', function ($q) use ($search) {
                    $search = strtolower("%$search%");
                    return $q->whereRaw('LOWER(name) LIKE ?', [$search]);
                });
                // satu lagi, check kalo dia berdasarkan id
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })

            ->when($status_delivery, function ($query, $status) {
                return $query->where('status_delivery', $status);
            })

            ->when($delivery_at, function ($query, $delivery_at) {
                return $query->where('delivery_at', 'LIKE', "%$delivery_at%");
            })

            // ->when($sort_by, function ($query, $sort_by) {
            //     return $this->sort_by($query, $sort_by);
            // })

            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })->paginate($limit);
    }

    public function byCustomer(
        string|null $search,
        string|null $category,
        string|null $sort_by,
        string|null $status,
        string|null $status_delivery,
        int $limit = 3,
        string|null $delivery_at
    ) {
        return Transaction::where('id_user', auth()->user()->id)
            ->with('orders')

            ->when($search, function ($query, $search) {
                return $query->whereHas('orders.menu_price.menu', function ($q) use ($search) {
                    $search = strtolower("%$search%");
                    return $q->whereRaw('LOWER(name) LIKE ?', [$search]);
                });
                // satu lagi, check kalo dia berdasarkan id
            })

            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })

            ->when($status_delivery, function ($query, $status) {
                return $query->where('status_delivery', $status);
            })

            ->when($delivery_at, function ($query, $delivery_at) {
                return $query->where('delivery_at', 'LIKE', "%$delivery_at%");
            })

            ->when($sort_by, function ($query, $sort_by) {
                return $this->sort_by($query, $sort_by);
            })

            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })->paginate($limit);
    }

    public function detail(
        string $id,
        bool $isCustomer = true,
        string|null $token = null,
    ) {
        return Transaction::where('id', $id)
            ->when($isCustomer, function ($query) use ($token) {
                $user = auth()->user();
                if ($user) {
                    return $query->where('id_user', auth()->user()->id);
                } else {
                    return $query->where('access_token', $token);
                }
            })
            ->with([
                'orders',
                'address',
                'user',
                'payment_proof'
            ])
            ->first();
    }

    public function create(
        array $data,
        bool $is_created_by_customer = true,
        UploadedFile|null $image = null,
    ) {
        // $data = $data['data'];
        DB::beginTransaction();
        $cloudinary = new CloudinaryClient();
        $uplouded = null;
        try {
            $createdTransaciton = Transaction::create([
                'id_user' => $data['user_info']['id'],
                'shipping_cost' => $data['transaction']['shipping_cost'],
                'subtotal' => $data['transaction']['sub_total'],
                'total_price' => $data['transaction']['sub_total'] + $data['transaction']['shipping_cost'],
                'total_items' => $data['transaction']['total_item'],
                'category' => $data['transaction']['category'],
                'note' => $data['transaction']['note'],
                'delivery_at' => $data['delivery_info']['delivery_at'],
                'contact_email' => $data['user_info']['email'],
                'created_at' => $data['transaction']['is_created'] ? $data['transaction']['created_at'] : now(),
                'updated_at' => now(),
            ]);

            // jika dibuat sama admin
            if (!$is_created_by_customer) {
                $createdTransaciton->is_guest = $data['transaction']['is_guest'];
                $createdTransaciton->access_token = $data['transaction']['is_guest'] ? Str::random(60) : null;
                // jika transaction itu success
                if ($data['transaction']['is_success']) {
                    $createdTransaciton->status = StatusTransaction::SUCCESS;
                    $createdTransaciton->status_delivery = StatusDelivery::DELIVERED;
                }
                // jika transaksi sudah ada sebelumnya
                if ($data['transaction']['is_created']) {
                    $createdTransaciton->setCreatedAt(Carbon::parse($data['transaction']['created_at']));
                }
                $is_transfer = $data['transaction']['payment_type'] !== 'transfer';
                if ($is_transfer && $image) {
                    $uplouded = $cloudinary->uploudPaymentProof($image->getRealPath());
                    if (!$uplouded) {
                        throw new Exception('Gagal Dalam Menyimpan Gambar');
                    }
                    TransactionPaymentProof::create([
                        'id_transaction' => $createdTransaciton->id,
                        'public_id' => $uplouded['public_id'],
                        'url' => $uplouded['secure_url'],
                        'reason' => 'Sudah Membayar',
                        'created_at' => now(),
                        'status' => TransactionPaymentProofStatus::ACCEPTED,
                        'updated_at' => now(),
                    ]);
                }

                $createdTransaciton->save();
            }


            foreach ($data['summary_orders']['items'] as $menu) {
                foreach ($menu['packages'] as $price) {
                    TransactionOrder::create([
                        'id_transaction' => $createdTransaciton->id,
                        'id_menu_price' => $price['id'],
                        'id_menu' => $menu['id'],
                        'total_price' => $price['quantity'] * $price['price'],
                        'price_at_purchase' => $price['price'],
                        'quantity' => $price['quantity'],
                        'note' => $price['note'] ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            $address = $data['delivery_info']['user_address'];
            $address_raw_data = [
                'id_transaction' => $createdTransaciton->id,
                'received_name' => $address['received_name'] ?? $address['fullname'],
                'phone' => $address['phone'],
                'label' => $address['label'],
                'address' => $address['address'],
                'note' => $address['note'] ?? '',
                'longitude' => $address['longitude'],
                'latitude' => $address['latitude'],
                'created_at' => now(),
                'updated_at' => now()
            ];
            TransactionAddress::create($address_raw_data);

            // update alamat & nomor telepon pengguna
            if (isset($address['save_to_profile']) && $address['save_to_profile']) {
                $address_raw_data['id_user'] = $data['user_info']['id'];
                $address_raw_data['is_main_address'] = $address['is_main_address'] ?? false;
                (new UserAddressService())->saveAndChangeTheMainAdress($address_raw_data);
            }

            // bahaya nich.....
            if (isset($data['is_changed']) && $data['is_changed']) {
                $data['user_info']->save();
            }

            DB::commit();

            // send email disini?

            Mail::to($createdTransaciton->contact_email)->send(new SuccessCreateTransactionEmail($createdTransaciton));

            return [
                'is_success' => true,
                'message' => 'Berhasil dalam membuat transaksi!',
                'user' => $data['user_info'],
                'transaction' => $createdTransaciton,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error when creating the transaction: ' . $e->getMessage());
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan dalam membuat transaksi'
            ];
        }
    }

    public function uploudPaymentProof(
        Transaction $transaction,
        UploadedFile|null $image,
    ) {

        if (!$image) {
            return [
                'is_success' => false,
                'message' => 'Membutuhkan gambar untuk menguploud bukti pembayaran!'
            ];
        }

        // kalo mau uploud payment proff harus pending kocak wkwkkwkw salah gw 

        if (StatusTransaction::tryFrom((string) $transaction->status) == StatusTransaction::PENDING) {
            if ($transaction->payment_proof && !$this->isPaymentProofRejected($transaction->payment_proof)) {
                return [
                    'is_success' => false,
                    'message' => 'Maaf, saat ini kamu tidak memenuhi syarat untuk menguploud ulang kembali bukti pembayaran'
                ];
            }
            // syarat masuk kesini adalaah
            // status transaksi masih waiting for invoice dan pending
            // jika status pending maka dan payment proof sudah ada maka status payment_proof harus reject
            $cloudinary = new CloudinaryClient();
            // uploud gambar disini!
            $uplouded = $cloudinary->uploudPaymentProof($image->getRealPath());
            $old_public_id = null;
            if (!$uplouded) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak berhasil dalam photo mengunggah bukti pembayaran'
                ];
            }

            try {
                // mulai transction
                DB::beginTransaction();
                // logic / ubah transaksi bakal mulai disnii
                $is_created = false;
                if ($transaction->payment_proof) {
                    $old_public_id = $transaction->payment_proof->public_id;

                    $transaction->payment_proof->url = $uplouded['secure_url'];
                    $transaction->payment_proof->status = TransactionPaymentProofStatus::WAIT_FOR_CONFIRMATION;
                    $transaction->payment_proof->reason = '';
                    $transaction->payment_proof->public_id = $uplouded['public_id'];

                    $transaction->payment_proof->save();
                    $cloudinary->deleteThePaymentProofImage($old_public_id);
                } else {
                    // kalo blm ada transaction payment proof
                    TransactionPaymentProof::create([
                        'id_transaction' => $transaction->id,
                        'public_id' => $uplouded['public_id'],
                        'url' => $uplouded['url'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $is_created = true;
                }

                $transaction->status = StatusTransaction::PENDING;
                $transaction->save();

                DB::commit();

                return [
                    'is_success' => true,
                    'is_created' => $is_created,
                    'message' => 'Berhasil dalam menguploud bukti pembayaran',
                ];

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error when try to create payment proof : ' . $e->getMessage());
                if ($uplouded) {
                    $deleted = $cloudinary->delete($uplouded['public_id']);
                }
                return [
                    'is_success' => false,
                    'message' => 'Telah Terjadi Kesalahan Pada Server',
                ];
            }

        }

        return [
            'is_success' => false,
            'message' => 'Transaksi tidak memenuhi persyaratan untuk menguploud bukti pembayaran!'
        ];

    }
    public function changeShippingCost(
        Transaction $transaction,
        int $shipping_cost
    ) {
        try {
            $isAcceptableStatus = $this->isAcceptableStatusForChangingShippingCost($transaction->status);
            // status_bukti_pembayaran yang aman itu => rejected,
            if ($transaction->payment_proof && !$this->isPaymentProofRejected($transaction->payment_proof) || !$isAcceptableStatus) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak bisa merubah ongkos kirim dikarenakan Status Transaksi Sudah Bukan Menunggu Invoice Dan Status Bukti Pembayaran Bukan Ditolak',
                ];
            }
            return DB::transaction(function () use ($transaction, $shipping_cost) {
                // cek terlebih dahulu statusnya!
                // waiting_for_invoice => aman

                $transaction->shipping_cost = $shipping_cost;
                $transaction->total_price = $transaction->subtotal + $shipping_cost;
                $transaction->status = StatusTransaction::PENDING;
                $transaction->save();

                return [
                    'is_success' => true,
                    'message' => 'Berhasil Merubah Ongkos Kirim & Total Harga Transaksi'
                ];
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When change the shipping cost : ' . $e->getMessage());
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan pada server saat ingin merubah ongkos kirim'
            ];
        }
    }

    public function rejectTransaction(
        Transaction $transaction,
        string $reason,
        bool $isManagement = true,
    ) {
        try {
            // apakah status transaksi masih waiting_for_invoice?
            if (!in_array(StatusTransaction::from((string) $transaction->status), [StatusTransaction::WAITING_FOR_INVOICE, StatusTransaction::PENDING]) || $transaction->payment_proof && !$this->isPaymentProofRejected($transaction->payment_proof)) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak Bisa Membatalkan Transaksi Syarat & Ketentuan Tidak Terpenuhi'
                ];
            }
            return DB::transaction(function () use ($transaction, $reason, $isManagement) {
                $transaction->status = $isManagement ? StatusTransaction::CANCELLED_BY_ADMIN : StatusTransaction::CANCELLED_BY_CUSTOMER;
                $transaction->cancelled_reason = $reason;
                $transaction->save();
                return [
                    'is_success' => true,
                    'message' => 'Berhasil Membatalkan Transaksi!'
                ];
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When tejecting the transaction : ' . $e->getMessage());
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan pada server saat ingin menolak transaksi'
            ];
        }
    }

    public function acceptThePaymentProof(
        Transaction $transaction,
        string $reason,
        UploadedFile|null $image,
    ) {
        try {
            // apakah bukti pembayaran sudah di accept?
            if (TransactionPaymentProofStatus::from((string) $transaction->payment_proof->status) === TransactionPaymentProofStatus::ACCEPTED) {
                return [
                    'is_success' => false,
                    'message' => 'Bukti pembayaran sudah diterima & valid!'
                ];
            }

            // cek apakah kita perlu untuk uploud?
            // cek dari data filenya
            $cloudinary = new CloudinaryClient();
            $new_proof = [
                'old_public_id' => $transaction->payment_proof->public_id,
                'public_id' => 0,
                'url' => '',
                'is_success' => false,
            ];
            if ($image) {
                $uplouded = $cloudinary->uploudPaymentProof($image->getRealPath());
                if (!$uplouded) {
                    return [
                        'is_success' => false,
                        'message' => 'Tidak Dapat Mengunggah Gambar Bukti Pembayaran'
                    ];
                }
                $new_proof['is_success'] = true;
                $new_proof['url'] = $uplouded['secure_url'];
                $new_proof['public_id'] = $uplouded['public_id'];
            }

            // jadi kita uploud image disini

            return DB::transaction(function () use ($transaction, $reason, $new_proof, $cloudinary) {
                $transaction->status = StatusTransaction::PAID;
                $transaction->status_delivery = StatusDelivery::PROCESS;
                $transaction->payment_proof->status = TransactionPaymentProofStatus::ACCEPTED;
                $transaction->payment_proof->reason = $reason;
                if ($new_proof['is_success']) {
                    $transaction->payment_proof->url = $new_proof['url'];
                    $transaction->payment_proof->public_id = $new_proof['public_id'];
                }
                $transaction->save();
                $transaction->payment_proof->save();

                // delete the image
                if ($new_proof['is_success']) {
                    $deleted_image = $cloudinary->deleteThePaymentProofImage($new_proof['old_public_id']);
                }

                // seharusnya perlu cek nih di satu stau setelah saving...
                $message = $new_proof['is_success'] ? 'Berhasil Menyetujui Bukti Pembayaran Customer Dan Mengubah Photo Bukti Pembayaran!' : 'Berhasil Menyetujui Bukti Pembayaran Customer';
                return [
                    'is_success' => true,
                    'message' => $message,
                ];
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When accepting the payment proof : ' . $e->getMessage());
            if ($uplouded) {
                $deleted = $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan pada server saat ingin menerima bukti pembayaran'
            ];
        }
    }

    public function rejectThePaymentProof(
        Transaction $transaction,
        string $reason
    ) {
        try {
            return DB::transaction(function () use ($transaction, $reason) {

                // apakah status reject?
                if ($this->isPaymentProofRejected($transaction->payment_proof)) {
                    return [
                        'is_success' => false,
                        'message' => 'Status Bukti Pembayaran Sudah Di Tolak'
                    ];
                }
                // apakah transaksi masih bisa menerima pergnatian harga? atau status transaksi sudah di byarkan namun masih di proses
                if ($this->isAcceptableStatusForChangingShippingCost($transaction->status) || $this->isTransactionStillInProcess($transaction)) {
                    $transaction->status = StatusTransaction::PENDING;
                    $transaction->status_delivery = StatusDelivery::WAIT_FOR_CONFIRMATION;
                    $transaction->save();
                    $transaction->payment_proof->status = TransactionPaymentProofStatus::REJECTED;
                    $transaction->payment_proof->reason = $reason;
                    $transaction->payment_proof->save();

                    // seharusnya perlu cek nih di satu stau setelah saving...

                    return [
                        'is_success' => true,
                        'message' => 'Berhasil Menolak Bukti Pembayaran!'
                    ];
                }

                // syarat tidak terpenuhi 
                return [
                    'is_success' => false,
                    'message' => 'Transaksi Sudah Tidak Dapat Menerima Pergantian Ongkos Kirim Atau Status Pengiriman Sudah Tidak Dalam Tahap Prosess Atau Dibawahnya'
                ];

            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When tejecting the payment proof : ' . $e->getMessage());
            return [
                'is_success' => false,
                'message' => 'Telah terjadi kesalahan pada server saat ingin menolak bukti pembayaran'
            ];
        }
    }

    public function changeStatusDelivery(
        Transaction $transaction,
        string $status,
    ) {
        $isValidStatusDelivery = StatusDelivery::tryFrom($status);
        if (!$isValidStatusDelivery) {
            return [
                'is_success' => false,
                'message' => 'Status Delivery Yang DIberikan Tidak Valid',
            ];
        }
        // di proses => menunggu_diambil => dianterin => sampai tujuan
        // saat diantarkan & sampai tujuan kirimkan email?
        if (StatusTransaction::from((string) $transaction->status) === StatusTransaction::SUCCESS) {
            return [
                'is_success' => false,
                'message' => 'Status Transaksi Sudah Selesai & Tidak Bisa Merubah Kembali Data',
            ];
        }
        $transaction->status_delivery = $isValidStatusDelivery;
        $transaction->save();
        return [
            'is_success' => true,
            'message' => 'Berhasil merubah Status Pengiriman Menjadi ' . $status
        ];
    }

    public function completeTheTransaction(
        Transaction $transaction
    ) {
        if (StatusTransaction::from((string) $transaction->status) === StatusTransaction::SUCCESS) {
            return [
                'is_success' => false,
                'message' => 'Transaksi Sudah Selesai, Tidak Perlu Diubah Kembali!'
            ];
        }
        // must paid, refund status must none or success, delivery status must delivered and payment proof must accepted
        // keki gini aja dlu kali ya hehe..
        if (StatusTransaction::from((string) $transaction->status) !== StatusTransaction::PAID) {
            return [
                'is_success' => false,
                'message' => 'Tidak Dapat Menyelesaikan Transaksi, Pastikan Status Transaksi Adalah Terbayar'
            ];
        }
        $transaction->status = StatusTransaction::SUCCESS;
        $transaction->save();
        return [
            'is_success' => true,
            'message' => 'Berhasil Merubah Status Transaksi Menjadi Success'
        ];
    }

    private function sort_by(
        mixed $query,
        string $sort_by
    ) {
        // harga termurah, termahal, dibuat terlama, dibuat skrng
        switch ($sort_by) {
            case 'lowest_price':
                return $query->orderBy('total_price', 'desc');
            case 'highest_price':
                return $query->orderBy('total_price');
            case 'old_created':
                return $query->orderBy('created_at', 'desc');
            case 'new_created':
                return $query->orderBy('created_at');
            default:
                return $query;
        }
    }

    private function isTransactionStillInProcess(Transaction $transaction)
    {
        return StatusTransaction::from((string) $transaction->status) === StatusTransaction::PAID
            && in_array(StatusDelivery::from((string) $transaction->status_delivery), [StatusDelivery::PROCESS, StatusDelivery::WAIT_FOR_CONFIRMATION]);
    }

    private function isAcceptableStatusForChangingShippingCost(string|StatusTransaction $status)
    {
        return in_array(StatusTransaction::from($status), [StatusTransaction::WAITING_FOR_INVOICE, StatusTransaction::PENDING]);
    }

    private function isPaymentProofRejected(TransactionPaymentProof $payment_proof)
    {
        return TransactionPaymentProofStatus::from((string) $payment_proof->status) === TransactionPaymentProofStatus::REJECTED;
    }


}