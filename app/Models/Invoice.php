<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UuidIdenty;

class Invoice extends Model
{
    use HasFactory, UuidIdenty;

    const PAYMENT_TYPE = 'multipayment';

    const INVO_STATUS = [
        'BELUM UPLOAD/BAYAR',
        'VERIFIKASI',
        'LUNAS'
    ];

    protected $fillable = [
        'uuid',
        'member_id', 
        'token', 
        'valid_until', 
        'status',
        'is_automatic',
        'payment_method',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'is_automatic' => 'boolean',
    ];

    public function scopeLunas($query)
    {
        return $query->where('status', 2);
    }

    public function isInvoiceLunas()
    {
        return $this->status == 2;
    }

    /**
     * Get the member that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function invoicedOrder()
    {
        return $this->hasOne(InvoicedOrder::class, 'invoice_id', 'id');
    }

    public function order()
    {
        return $this->hasOneThrough(
            Order::class,
            InvoicedOrder::class,
            'invoice_id',
            'id',
            'id',
            'order_id'
        );
    }
}
