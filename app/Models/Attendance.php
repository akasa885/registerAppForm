<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_path',
        'link_id',
        'active_from',
        'active_until',
        'confirmation_mail',
        'with_verification_certificate',
        'price_certificate',
        'is_using_payment_gateway',
        'allow_non_register',
        'created_by',
    ];

    protected $casts = [
        'active_from' => 'datetime',
        'active_until' => 'datetime',
        'with_verification_certificate' => 'boolean',
        'is_using_payment_gateway' => 'boolean',
    ];

    public function isCertNeedVerification()
    {
        return $this->with_verification_certificate;
    }

    public function scopeOwnAttendance($query)
    {
        return $query->where('created_by', auth()->user()->id);
    }

    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    public function member_attend()
    {
        return $this->hasMany(MemberAttend::class, 'attend_id', 'id');
    }
}
