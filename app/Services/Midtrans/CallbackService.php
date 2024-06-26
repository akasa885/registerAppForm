<?php

namespace App\Services\Midtrans;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\Midtrans\Midtrans;
use App\Http\Traits\FormatNumberTrait;
use Midtrans\Notification;

class CallbackService extends Midtrans
{
    use FormatNumberTrait;
    protected $notification;
    protected $order;
    protected $serverKey;
        

    public function __construct()
    {
        parent::__construct();

        $this->serverKey = config('midtrans.server_key');
        $this->_handleNotification();
    }

    public function isSignatureKeyVerified()
    {
        return ($this->_createLocalSignatureKey() == $this->notification->signature_key);
    }

    public function isSuccess()
    {
        $statusCode = $this->notification->status_code;
        $transactionStatus = $this->notification->transaction_status;
        $fraudStatus = !empty($this->notification->fraud_status) ? ($this->notification->fraud_status == 'accept') : true;

        return ($statusCode == 200 && $fraudStatus && ($transactionStatus == 'capture' || $transactionStatus == 'settlement'));
    }

    public function isPending()
    {
        return ($this->notification->transaction_status == 'pending');
    }

    public function isExpire()
    {
        return ($this->notification->transaction_status == 'expire');
    }

    public function isCancelled()
    {
        return ($this->notification->transaction_status == 'cancel');
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getPaymentType()
    {
        return $this->notification->payment_type;
    }

    protected function _createLocalSignatureKey()
    {
        $orderId = $this->order->order_number;
        $statusCode = $this->notification->status_code;
        $grossAmount = $this->changeIntegerIntoDecimalTwo($this->order->net_total);
        $serverKey = $this->serverKey;
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');

        return $signature;
    }

    protected function _handleNotification()
    {
        $notification = new Notification();

        $orderNumber = $notification->order_id;
        $order = Order::where('order_number', $orderNumber)->first();

        $this->notification = $notification;
        $this->order = $order;
    }

}