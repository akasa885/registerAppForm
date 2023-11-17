<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UuidIdenty;

class Order extends Model
{
    use HasFactory, UuidIdenty;

    const STATUS = [
        'pending', 'processing', 'completed', 'decline', 'cancel', 'void'
    ];

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

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
