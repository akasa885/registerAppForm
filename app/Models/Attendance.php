<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\FormatNumberTrait;

class Attendance extends Model
{
    use HasFactory, FormatNumberTrait;

    protected $fillable = [
        'attendance_path',
        'link_id',
        'active_from',
        'active_until',
        'confirmation_mail',
        'with_verification_certificate',
        'price_certificate',
        'is_using_payment_gateway',
        'payment_information',
        'category',
        'allow_non_register',
        'created_by',
    ];

    protected $casts = [
        'active_from' => 'datetime',
        'active_until' => 'datetime',
        'with_verification_certificate' => 'boolean',
        'is_using_payment_gateway' => 'boolean',
    ];

    protected $appends = [
        'formatted_price_certificate',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attendance) {
            if ($attendance->with_verification_certificate) {
                $attendance->category = 'certificate';
            }
        });
    }

    public function getFormattedPriceCertificateAttribute()
    {
        return $this->priceWithCurrencyAndDecimal($this->price_certificate);
    }

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

    public function ordered()
    {
        return $this->morphMany(OrderDetail::class, 'orderable');
    }
}
