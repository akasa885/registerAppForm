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
                'order_id' => $this->order->order_id,
                'gross_amount' => $this->order->net_total,
            ],
            'item_details' => $this->setDetailsOfOrder(),
            'customer_details' => $this->setCustomerInformation(),
            'callbacks' => [
                'finish' => route('payments.callback.status.page')."?order_id={$this->order->order_id}&customer_id=".Crypt::encryptString($this->order->account->id)."&status=success",
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
                'price' => $this->countPriceDetailIncluceTax($item->price)['total'],
                'quantity' => $item->qty,
                'name' => $item->name,
            ];
        }

        return $data;
    }

    private function setCustomerInformation(): array
    {
        $account = $this->order->account;
        
        return [
            'first_name' => $account->org_name,
            'email' => $account->pic_email,
            'phone' => $account->org_phone,
        ];
    }
}