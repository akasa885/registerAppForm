<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberTrash extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'link_id',
        'contact_number',
        'domisili',
        'corporation',
        'bukti_bayar',
        'deleted_time'
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    public function restoreMember()
    {
        $member = Member::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'link_id' => $this->link_id,
            'contact_number' => $this->contact_number,
            'domisili' => $this->domisili,
            'corporation' => $this->corporation,
            'bukti_bayar' => $this->bukti_bayar
        ]);

        $this->delete();

        return $member;
    }
}
