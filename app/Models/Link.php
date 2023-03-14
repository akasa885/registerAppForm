<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;
    const TOKEN_LENGTH = 5;
    const LINK_TYPE = ['pay', 'free'];

    protected $fillable = [
        'link_path', 'title', 'description', 'banner', 'active_from', 'active_until', 'created_by'
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

    public function scopeLatestFirst($query)
    {
        return $query->orderBy('id','DESC');
    }

    public static function filterActiveMyLinks()
    {
        $user = auth()->user();
        $links = $user->links;
        $date = date("Y-m-d");
    
        return $links->filter(function ($link) use ($date) {
            return date("Y-m-d", strtotime($link->active_from)) <= $date && date("Y-m-d", strtotime($link->active_until)) >= $date;
        });
    }

    /**
     * Get the user that owns the Link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ownsMember(Member $member)
    {
      return $this->id === $member->link->id;
    }

    /**
     * Get all of the mails for the Link
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mails(): HasMany
    {
        return $this->hasMany(MailPayment::class, 'link_id', 'id');
    }

    public function getNumber()
    {
        return $this->members->count('email');
    }
}
