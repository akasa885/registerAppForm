<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAttend extends Model
{
    use HasFactory;

    protected $fillable = [
        'attend_id',
        'member_id',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attend_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
