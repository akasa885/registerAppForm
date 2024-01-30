<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;
use Illuminate\Support\Facades\Crypt;
use App\Http\Traits\OrderedDetailTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Exception;

class CreateSnapTokenService extends Midtrans
{
    use OrderedDetailTrait;

    protected $order;
    private $gopaySettings = false;
    public $redirectUrlSnap;
    private $paramsSnap;
    public $transaction = null;

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
            'credit_card' => [
                'secure' => true,
            ],
            'item_details' => $this->setDetailsOfOrder(),
            'customer_details' => $this->setCustomerInformation(),
            'callbacks' => [
                'finish' => route('payments.callback.status.page')."?order_id={$this->order->order_number}&customer_id=".Crypt::encryptString($this->order->member_id),
            ]
        ];

        if($this->gopaySettings) {
            $params = array_merge($params, $this->withGopayCallbacks());
        }

        $this->paramsSnap = $params;

        if (is_null($this->transaction)) {
            $transaction = $this->createTransaction();
        }

        $snapToken = $this->transaction->token;

        return $snapToken;
    }

    private function createTransaction()
    {
        $transaction = Snap::createTransaction($this->paramsSnap);

        $this->transaction = $transaction;

        return $transaction;
    }

    public function getSnapUrl()
    {
        $snapUrl = $this->transaction->redirect_url;

        return $snapUrl;
    }

    private function withGopayCallbacks()
    {
        return [
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => route('payments.callback.status.page')."?order_id={$this->order->order_number}&customer_id=".Crypt::encryptString($this->order->member_id),
            ]
        ];
    }

    /**
     * Get snap token with gopay callbacks
     * @return string 
     * @throws BindingResolutionException 
     * @throws Exception 
     */
    public function getSnapTokenWithGopay()
    {
        $this->gopaySettings = true;

        return $this->getSnapToken();
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
                'category' => $item->orderable->category,
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