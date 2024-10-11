<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Traits\FormatNumberTrait;
use Carbon\Carbon;

class Link extends Model
{
    use HasFactory, FormatNumberTrait;
    const TOKEN_LENGTH = 5;
    const LINK_TYPE = ['pay', 'free'];

    protected $fillable = [
        'link_path', 
        'title', 
        'description',
        'registration_info',
        'banner', 
        'bank_information',
        'active_from', 
        'active_until',
        'event_date',
        'created_by',
        'link_type',
        'category',
        'price',
        'has_member_limit',
        'member_limit',
        'is_multiple_registrant_allowed',
        'sub_member_limit',
        'hide_events',
    ];

    protected $dates = [
        'active_from',
        'active_until',
        'event_date'
    ];

    protected $casts = [
        'has_member_limit' => 'boolean',
        'is_multiple_registrant_allowed' => 'boolean',
        'hide_events' => 'boolean',
    ];

    protected $appends = [
        'price_formatted',
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

    public function getPriceFormattedAttribute()
    {
        return $this->priceWithCurrencyAndDecimal($this->price);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderBy('id','DESC');
    }

    public function scopeIsLinkViewable($query)
    {
        $date = date("Y-m-d");
        return $query->where(function ($query) use ($date) {
            $query->where('hide_events', 0);
        });
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

    public static function myLinksEventRange()
    {
        // get my links based on user, then filter by event date range.
        $user = auth()->user();
        $links = $user->links;
        $date = date("Y-m-d");

        // condition will true if today, is before or equal to event date
        // or, event date add day 1 days

        return $links->filter(function ($link) use ($date) {
            return date("Y-m-d", strtotime($link->event_date)) >= $date && date("Y-m-d", strtotime($link->event_date)) <= date("Y-m-d", strtotime($link->event_date . ' + 1 days'));
        });
    }

    public function isLinkViewable()
    {
        if (!$this->hide_events) {
            return true;
        } else {
            $date = date("Y-m-d");
            // if today is after 3 days from event_date until, then link is viewable
            return date("Y-m-d", strtotime($this->event_date)) < date("Y-m-d", strtotime($date));
        }
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

    public function ordered()
    {
        return $this->morphMany(OrderDetail::class, 'orderable');
    }
}
