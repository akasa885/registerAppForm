<?php
namespace App\Http\Traits;

use App\Models\Order;
use App\Models\OrderDetail;

trait OrderCustomTrait {
    public function createDuplicateOrder($order) {
        $preDupOrder = $order->prepDupOrder();
        $orderDetails = $order->orderDetails;

        $newOrder = Order::create($preDupOrder->toArray());
        $dupOrdet = collect();
        foreach ($orderDetails as $item) {
            $dupOrdet->push($item->prepDupOrderDetail());
        }
        $newOrder->orderDetails()->saveMany($dupOrdet);

        return $newOrder;
    }
}