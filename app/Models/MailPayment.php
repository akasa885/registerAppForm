<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailPayment extends Model
{
    use HasFactory;

    const TYPE_INFORMATION = ['confirmation', 'received', 'confirmed', 'reminder'];

    protected $fillable = ['link_id', 'information'];

    /**
     * Get the link that owns the MailPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function links(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'list_id', 'id');
    }
}
