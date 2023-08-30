<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    const INVO_STATUS = [
        'BELUM UPLOAD',
        'VERIFIKASI',
        'LUNAS'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    protected $fillable = ['member_id', 'token', 'valid_until', 'status'];

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
}
