<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UuidIdenty;
use App\Http\Traits\FormatNumberTrait;

class Order extends Model
{
    use HasFactory, UuidIdenty, FormatNumberTrait;

    const STATUS = [
        'pending', 'processing', 'completed', 'decline', 'cancel', 'void'
    ];

    // order_number : ex. ORD.2023.1118.0001.0001
    // ORD: order
    // 2023: year
    // 1118: month.day
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
        'member_id',
        'invoice_id',
        'snap_token_midtrans',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = $model->generateOrderNumber();
        });
    }

    public function generateOrderNumber()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $orderSequence = $this->generateOrderSequence();
        $memberId = $this->member_id;

        return "ORD.$year.$month$day.$orderSequence.$memberId";
    }

    public function generateOrderSequence()
    {
        $order = Order::whereDate('created_at', date('Y-m-d'))->count();
        $orderSequence = $order + 1;

        return $this->addZeroPrefix(4, $orderSequence);
    }



    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
