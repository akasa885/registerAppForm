<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;
use Illuminate\Support\Facades\Crypt;
use App\Http\Traits\OrderedDetailTrait;

class CreateSnapTokenService extends Midtrans
{
    use OrderedDetailTrait;

    protected $order;

    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->order_number,
                'gross_amount' => $this->order->net_total,
            ],
            'item_details' => $this->setDetailsOfOrder(),
            'customer_details' => $this->setCustomerInformation(),
            'callbacks' => [
                'finish' => route('payments.callback.status.page')."?order_id={$this->order->order_number}&customer_id=".Crypt::encryptString($this->order->member_id)."&status=success",
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }

    private function setDetailsOfOrder(): array
    {
        $details = $this->order->orderDetails;
        $details->load('orderable');
        $data = [];

        foreach($details as $item) {
            $data[] = [
                'id' => $item->id,
                'price' => $item->price,
                'quantity' => $item->qty,
                'name' => $item->name,
            ];
        }

        return $data;
    }

    private function setCustomerInformation(): array
    {
        $registrant = $this->order->member;
        
        return [
            'first_name' => $registrant->full_name,
            'email' => $registrant->email,
            'phone' => $registrant->contact_number,
        ];
    }
}