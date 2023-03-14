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
        'created_by',
    ];

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
