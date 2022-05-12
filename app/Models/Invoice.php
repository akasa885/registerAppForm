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

    protected $fillable = ['member_id', 'token', 'valid_until', 'status'];

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
