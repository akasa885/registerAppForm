<?php

namespace App\Http\Traits;

use App\Models\Order;
use App\Services\Midtrans\CreateSnapTokenService;

trait SnapTokenCreate {
    public function createTransaction($order)
    {
        try {
            $snapToken = $order->snap_token_midtrans;
            if (is_null($snapToken)) {
                $midtrans = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapTokenWithGopay();
                $snapUrl = $midtrans->getSnapUrl();

                $order->snap_token_midtrans = $snapToken;
                $order->snap_redirect = $snapUrl;
                $order->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
            \Log::error('Failed to create snap token: '.$th->getMessage());
            report($th);
        }
    }
}