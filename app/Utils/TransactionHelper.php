<?php

namespace App\Utils;

use App\ResponseData;
use App\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionHelper
{
    private ResponseData $responseData;

    /**
     * Create a new class instance.
     */

    public function __construct() {
        $this->responseData = new ResponseData();
    }

    public function getCategoryTransaction($orderedMenus)
    {
        $category = TransactionCategory::ORDER;

        // kalo gw ya... kita bisa pre order menu mingguan dengan minimal 50 box dan jika dikirimkan hari minggu maka minimal 100 box;
        // jadi cara ceknya adalah, kita tentuin terlebih dahulu.. jika hanya ada semua menunya adalah mingguan maka minimal pemesanan box berdasarkan paket.

        foreach ($orderedMenus as $order) {
            if ($order->category === 'non-weekly') {
                $category = TransactionCategory::PRE_ORDER;
                break;
            }
        }

        return $category;
    }

    public function getMinimumOrder(
        $orderedMenus,
        TransactionCategory|string $category,
        Carbon $delivery_at,
    ) {
        if ($category === TransactionCategory::ORDER) {
            foreach ($orderedMenus as $order) {
                foreach ($order->prices as $price) {
                    // ini bisa bikin bingung si penggunaan '-' wkkw. intinya cek apakah quantity dibawah minimum_order
                    $minumum_order = $price->package->minimum_order;
                    if ($price->quantity < $minumum_order) {
                        return $this->responseData->create(
                            'Paket: ' . $price->package->name . ' ' . $order->name . ' Minimal Pemesanan Adalah ' . $minumum_order,
                            status: 'warning',
                            status_code: 400,
                            isJson: false
                        );
                    }
                }
            }
        } else if ($category === TransactionCategory::PRE_ORDER) {
            foreach ($orderedMenus as $order) {
                $minumum_order = $this->checkMinimumPreOrder($delivery_at);
                foreach ($order->prices as $price) {
                    // ini bisa bikin bingung si penggunaan '-' wkkw. intinya cek apakah quantity dibawah minimum_order
                    if ($price->quantity < $minumum_order) {
                        return $this->responseData->create(
                            'Paket: ' . $price->package->name . ' ' . $order->name . ' Minimal Pemesanan Adalah ' . $minumum_order,
                            status: 'warning',
                            status_code: 400,
                            isJson: false
                        );
                    }
                }
            }
        }
        return $this->responseData->create('Syarat & Ketentuan Sudah Terpenuhi', isJson: false);
    }

    public function checkMinimumPreOrder(Carbon $delivery_at)
    {
        // dilihat dari tanggal pengiriman
        // default:50 jika hari weekened itu menjadi 100;
        $minimum_order = 50;
        if ($delivery_at->isWeekend()) {
            $minimum_order = 100;
        }
        return $minimum_order;
    }

    public function canOrderAtThisTime(Carbon $delivery_at)
    {
        $today = Carbon::today()->setTime(15,0,0);

        if($delivery_at->isTomorrow() || $delivery_at->greaterThan(now()->addDays(1)) && now()->greaterThan($today)){
            return false;
        }
        return true;
    }

    public function countTotalPrice(Collection $menus){
        $total_price = 0;
        foreach($menus as $menu){
            foreach($menu->prices as $price){
                $total_price += $price->price * $price->quantity;
            }
        }
        return $total_price;
    }

}
