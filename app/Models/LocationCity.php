<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LocationCity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'slug',
        'state_id',
        'name'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($city) {
            $city->slug = Str::slug($city->name);
        });
    }
    
    public function state()
    {
        return $this->belongsTo(LocationState::class);
    }
}
