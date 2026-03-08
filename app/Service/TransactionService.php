<?php

namespace App\Service;

use App\Models\Transaction;
use App\Models\TransactionAddress;
use App\Models\TransactionOrder;
use Illuminate\Support\Facades\DB;

class TransactionService
{

    // note buat sekarang... semua transaksi minimal mengambil satu gambar menu makanan dari order yng sudha ada

    public function all()
    {
        return Transaction::all();
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

    public function detail(string $id)
    {
        return Transaction::where([
            'id' => $id,
            'id_user' => auth()->user()->id,
        ])
            ->with([
                'orders',
                'address',
                'payment_proof'
            ])
            ->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['transaction']['created_at'] = now();
            $data['transaction']['updated_at'] = now();
            $createdTransaciton = Transaction::create($data['transaction']);
            foreach ($data['items'] as $item) {
                foreach ($item->prices as $price) {
                    TransactionOrder::create([
                        'id_transaction' => $createdTransaciton->id,
                        'id_menu_price' => $price->id,
                        'id_menu' => $item->id,
                        'total_price' => $price->quantity * $price->price,
                        'price_at_purchase' => $price->price,
                        'quantity' =>  $price->quantity,
                        'note' =>  $price->note ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            TransactionAddress::create([
                'id_transaction' => $createdTransaciton->id,
                'received_name' => $data['address']->received_name,
                'phone' => $data['address']->phone,
                'label' => $data['address']->label,
                'address' => $data['address']->address,
                'note' => $data['address']->note,
                'longitude' => $data['address']->longitude,
                'latitude' => $data['address']->latitude,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return $createdTransaciton;
        });
    }

    public function cancell()
    {
        // ngambil sesi sekarang aja... bisa dari admin / customer 
        // cancell ketika transaksi pending atau bisa pas paid (refund)?
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


}