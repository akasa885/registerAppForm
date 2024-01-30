<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UuidIdenty;
use App\Http\Traits\FormatNumberTrait;

class Order extends Model
{
    use HasFactory, UuidIdenty, FormatNumberTrait;

    const TAX_RATE = 11;

    const STATUS = [
        'pending', 'processing', 'completed', 'decline', 'cancel', 'void'
    ];

    // order_number : ex. ORD.2023.1118.TCK.0001.0001
    // ORD: order
    // 2023: year
    // 1118: month.day
    // TCK: ticket, CRT: certificate
    // 0001: order sequence same day
    // 0001: member id

    protected $fillable = [
        'order_number',
        'uuid',
        'member_id',
        'name',
        'short_description',
        'gross_total',
        'discount',
        'tax',
        'net_total',
        'status',
        'invoice_id',
        'snap_token_midtrans',
        'snap_redirect',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'gross_total' => 'integer',
        'discount' => 'integer',
        'tax' => 'integer',
        'net_total' => 'integer',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $hidden = [
        'id',
        'snap_token_midtrans',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->order_number == null)
                $model->order_number = $model->generateOrderNumber();
        });
    }

    public function generateOrderNumber($categoryCode = null, $memberId = null)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $orderSequence = $this->generateOrderSequence();
        $memberId = $this->member_id ? $this->member_id : $memberId;
        if (!$categoryCode)
            return "ORD.$year.$month$day.$orderSequence.$memberId";
        else
            return "ORD.$year.$month$day.$categoryCode.$orderSequence.$memberId";
    }

    public function generateOrderSequence()
    {
        $order = Order::whereDate('created_at', date('Y-m-d'))->count();
        $orderSequence = $order + 1;

        return $this->addZeroPrefix(4, $orderSequence);
    }

    public function prepDupOrder(): Order
    {
        //this will prepare to duplicate order. without id, order_id, snap_token_midtrans, paid_at
        $order = new Order;
        // check orderNumber contains 'TCK' or 'CRT'
        if (strpos($this->order_number, 'TCK') !== false) {
            $order->order_number = $order->generateOrderNumber('TCK', $this->member_id);
        } else if (strpos($this->order_number, 'CRT') !== false) {
            $order->order_number = $order->generateOrderNumber('CRT', $this->member_id);
        } else {
            $order->order_number = $order->generateOrderNumber();
        }

        $order->name = $this->name;
        $order->short_description = $this->short_description;
        $order->gross_total = $this->gross_total;
        $order->discount = $this->discount;
        $order->tax = $this->tax;
        $order->net_total = $this->net_total;
        $order->status = 'pending';
        $order->member_id = $this->member_id;
        $order->invoice_id = $this->invoice_id;

        return $order;
    }


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
