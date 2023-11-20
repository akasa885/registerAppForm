<?php

namespace App\Http\Traits;

use App\Models\Order;
use App\Models\OrderDetail;

trait OrderedDetailTrait {
    public function storeOrderDetail($orderableModel, $order_id, $order_detail):void
    {
        // relation is ordered(). check if the orderable have method ordered()
        if (method_exists($orderableModel, 'ordered')) {
            $orderableModel->ordered()->create([
                'order_id' => $order_id,
                'orderable_id' => $orderableModel->id,
                'orderable_type' => $orderableModel->getMorphClass(),
                'name' => $order_detail['name'],
                'short_description' => $order_detail['short_description'] ?? 'ordered product',
                'price' => $order_detail['price'],
                'qty' => $order_detail['qty'],
                'total' => $order_detail['total'],
            ]);
            // dd($orderableModel->morphClass);
        } else {
            throw new \Exception('The model does not have method ordered()');
        }
    }

    public function countPriceDetailIncluceTax(int $price)
    {
        $taxRate = Order::TAX_RATE;
        $tax = $price * $taxRate / 100;
        $total = $price + $tax;

        return [
            'tax' => $tax,
            'total' => $total,
        ];
    }
}