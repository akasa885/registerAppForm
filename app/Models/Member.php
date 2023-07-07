<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    const PAYMENT_TOKEN_LENGTH = 10;

    protected $fillable = [
        'link_id', 'prefix', 'full_name', 'first_name', 'last_name',
        'suffix', 'email', 'contact_number', 'corporation', 'bukti_bayar', 'lunas',
        'question1', 'question2', 'question3',
    ];

    /**
     * Get the link that owns the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }


    /**
     * Get the invoices associated with the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoices()
    {
        return $this->hasOne(Invoice::class, 'member_id', 'id');
    }
}
