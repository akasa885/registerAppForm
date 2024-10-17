<?php

namespace App\Http\Traits;

use App\Helpers\GenerateStringUnique;;

use App\Models\Order;
use App\Models\Link;
use App\Models\Member;
use App\Models\Invoice;
use Carbon\Carbon;

use App\Http\Traits\GenerateTokenUniqueColumnTrait;
use App\Http\Traits\OrderedDetailTrait;

trait FormRegistrationTrait
{
    use GenerateTokenUniqueColumnTrait, OrderedDetailTrait;

    /**
     * 
     * @param mixed $members : registered of current link
     * @param mixed $email : email of new member
     * @param mixed $link : current link registration
     * @return bool 
     */
    public function AvailableMemberOnEvent($members, $email, $link)
    {
        $matched = 0;
        foreach ($members as $item) {
            if ($item->email == $email) {
                $matched += 1;
            }
        }

        if ($matched == 0) {
            return false;
        } else {
            if ($link->link_type == 'pay') {
                $member = Member::where('email', $email)->where('link_id', $link->id)->first();
                if ($member->invoices->status == 2) {
                    return true;
                } else if ($member->invoices->status == 1) {
                    return true;
                } 

                return false;
            }
            return true;
        }
    }

    public function isRegistrationMemberQuota($member, $quota)
    {
        if (count($member) < $quota) {
            return true;
        } else {
            return false;
        }
    }

    public function isRegistrationPaidMemberQuota($member, $quota)
    {
        $paid = 0;
        $now = Carbon::now();
        foreach ($member as $item) {
            // check if invoice is paid (2) or not expired
            if ($item->invoices->status == 2 || $item->invoices->status == 1 || $item->invoices->valid_until > $now) {
                $paid += 1;
            }
        }

        if ($paid < $quota) {
            return true;
        } else {
            return false;
        }
    }

    public function countTotalRegisteredMemberOnEvent($member)
    {
        return count($member);
    }

    public function createInvoice(Member $member, Link $link)
    {
        $invoice = new Invoice;
        $invoice->member_id = $member->id;
        $invoice->token = GenerateStringUnique::make('Invoice', 'token')->getToken(Member::PAYMENT_TOKEN_LENGTH);
        $currentDateTime = Carbon::now();
        $newDateTime = Carbon::now()->addHours(24);
        $invoice->valid_until = $newDateTime;
        $invoice->status = 0;
        $invoice->save();

        $this->createOrder($invoice, $link, $member);

        return $invoice;
    }

    private function createOrder($invoice, $link, $member): void
    {
        $order = [
            'order_number' => (new Order)->generateOrderNumber('TCK', $member->id),
            'member_id' => $member->id,
            'name' => 'Ticket Registration',
            'short_description' => 'Ticket ' . $link->title,
            'gross_total' => $link->price,
            'discount' => 0,
            'tax' => 0,
            'net_total' => $link->price,
            'status' => 1,
            'invoice_id' => $invoice->id,
            'snap_token_midtrans' => null,
            'due_date' => $invoice->valid_until,
        ];

        $order = $invoice->order()->create($order);

        $invoice->invoicedOrder()->create([
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->storeOrderDetail($link, $order->id, [
            'name' => 'Ticket Registration ',
            'short_description' => 'Ticket Registration ' . $link->title,
            'price' => $link->price,
            'qty' => 1,
            'total' => $link->price,
        ]);
    }
}
