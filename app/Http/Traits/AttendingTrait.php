<?php

namespace App\Http\Traits;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\AttendPaymentStore;
use Carbon\Carbon;

use App\Http\Traits\OrderedDetailTrait;

trait AttendingTrait {

    use OrderedDetailTrait;

    public function createOrderCertificate($attendance, $member, $changedFullName = null)
    {
        $order = Order::create([
            'order_number' => (new Order)->generateOrderNumber('CRT', $member->id),
            'name' => 'Certificate Payment',
            'short_description' => 'Certificate '.$attendance->link->title,
            'gross_total' => $attendance->price_certificate,
            'discount' => 0,
            'tax' => 0,
            'net_total' => $attendance->price_certificate,
            'status' => 1,
            'invoice_id' => null,
            'snap_token_midtrans' => null,
            'member_id' => $member->id,
            'due_date' => Carbon::now()->addMinutes(AttendPaymentStore::TIMEWAIT),
        ]);

        $this->storeOrderDetail($attendance, $order->id, [
            'name' => 'Certificate Payment',
            'short_description' => 'Certificate '.$attendance->link->title,
            'price' => $attendance->price_certificate,
            'qty' => 1,
            'total' => $attendance->price_certificate,
        ]);

        $tempAttend = AttendPaymentStore::create([
            'changed_full_name' => $changedFullName,
            'attend_id' => $attendance->id,
            'member_id' => $member->id,
            'order_id' => $order->id,
            'due_date' => $order->due_date,
        ]);

        return $order;
    }
}