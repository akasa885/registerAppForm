<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasFactory;

    const EMAIL_FROM = "no-reply@upquality.net";
    const TYPE_EMAIL = ['confirmation_pay', 'reminder_pay', 'confirmed_pay', 'event_info', 'attendance_confirmation'];

    protected $fillable = ['send_from', 'send_to', 'message', 'type_email', 'user_id', 'sent_count'];

    /**
     * Get the user that owns the Email
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
