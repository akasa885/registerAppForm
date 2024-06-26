<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    const PAYMENT_TOKEN_LENGTH = 10;

    protected $fillable = [
        'link_id','prefix', 'full_name', 'first_name', 'last_name',
        'suffix', 'email', 'contact_number', 'domisili', 'corporation', 'bukti_bayar', 'lunas'
    ];

    public function setDomisiliAttribute($value)
    {
        $this->attributes['domisili'] = strtolower($value);
    }

    public function setCorporationAttribute($value)
    {
        $this->attributes['corporation'] = strtolower($value);
    }

    public function getDomisiliAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function getCorporationAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function getCertificateOrder()
    {
        return $this->order()->where('order_number', 'like', '%CRT%')->first();
    }

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

    /**
     * Get the subMembers associated with the Member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subMembers()
    {
        return $this->hasMany(SubMember::class, 'member_id', 'id');
    }

    public function certbuy()
    {
        return $this->hasOne(AttendPaymentStore::class, 'member_id', 'id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'member_id', 'id');
    }
}
