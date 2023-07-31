<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'full_name',
        'email',
        'contact_number',
        'domisili',
        'corporation',
    ];

    public function registrant()
    {
        return $this->belongsTo(Member::class);
    }
}
