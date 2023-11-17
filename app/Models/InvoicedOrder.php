<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoicedOrder extends Pivot
{
    protected $table = 'invoiced_orders';

    protected $fillable = [
        'order_id',
        'invoice_id',
    ];

    protected $hidden = [
        'id',
        'order_id',
        'invoice_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
