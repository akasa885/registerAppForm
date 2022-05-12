<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_path', 'title', 'description', 'banner', 'active_from', 'active_until'
    ];

    /**
     * Get all of the members for the Link
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(Member::class, 'link_id', 'id');
    }

    public function ownsMember(Member $member)
    {
      return $this->id === $member->link->id;
    }

    /**
     * Get the mails associated with the Link
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mails()
    {
        return $this->hasOne(MailPayment::class, 'link_id', 'id');
    }

    public function getNumber()
    {
        return $this->members->count('email');
    }
}
