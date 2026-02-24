<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'total_price' => $this->total_price,
            'category' => $this->category,
            'status' => $this->status,

            'delivery_info' => [
                'status' => $this->status_delivery,
                'delivery_at' => $this->delivery_at
            ],

            'refund_info' => [
                'status' => $this->refund_statis,
                'reason' => $this->refund_reason,
                'is_refund' => $this->isRefund($this->status),
            ],

            'items' => $this->orders->map(function ($order) {
                return [
                    'name' => $order->menu_price->menu->name,
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                    // 'per_price' => $order->total_price / $order->quantity,
                    'vegetable' => $order->menu_price->menu->vegetable,
                    'side_dish' => $order->menu_price->menu->side_dish,
                    'chili_sauce' => $order->menu_price->menu->chili_sauce,
                    'image_url' => $order->menu_price->menu->image_url,
                    'theme' => $order->menu_price->menu->theme->name,
                    'categories' => $order->menu_price->menu->menu_categories->map(function ($category) {
                        return $category->categories->name;
                    }),
                    'package' => $order->menu_price->package->name,
                ];
            }),

            'address' => [
                'received_name' => $this->address->received_name,
                'phone' => $this->address->phone,
                'label' => $this->address->label,
                'address' => $this->address->address,
                'note' => $this->address->note,
            ],

            'payment_proof' => $this->payment_proof ? [
                'id' => $this->payment_proof->id,
                'url' => $this->payment_proof->url,
                'reason' => $this->payment_proof->reason,
                'status' => $this->payment_proof->status
            ] : null,

            'created_at' => $this->created_at,
        ];
    }


    private function isRefund(string $status): bool
    {
        return $status == 'cancelled_by_customer' || $status == 'cancelled_by_admin';
    }
}
