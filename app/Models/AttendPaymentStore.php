<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendPaymentStore extends Model
{
    use HasFactory;

    const TIMEWAIT = 15; // minutes;

    protected $fillable = [
        'changed_full_name',
        'attend_id',
        'member_id',
        'order_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attendPaymentStore) {
            $attendPaymentStore->due_date = Carbon::now()->addMinutes(self::TIMEWAIT);
        });
    }

    public function attend()
    {
        return $this->belongsTo(Attendance::class, 'attend_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
