<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UuidIdenty;

class OrderDetail extends Model
{
    use HasFactory, UuidIdenty;

    protected $fillable = [
        'uuid',
        'order_id',
        'orderable_id',
        'orderable_type',
        'name',
        'short_description',
        'price',
        'qty',
        'total',
    ];

    protected $casts = [
        'price' => 'integer',
        'qty' => 'integer',
        'total' => 'integer',
    ];

    protected $hidden = [
        'id',
    ];

    public function prepDupOrderDetail(): OrderDetail
    {
        $orderDetail = new OrderDetail;
        $orderDetail->fill($this->toArray());
        $orderDetail->order_id = null;
        $orderDetail->uuid = null;

        return $orderDetail;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderable()
    {
        return $this->morphTo();
    }
}
