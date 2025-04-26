<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LocationState extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'slug',
        'name'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($state) {
            $state->slug = Str::slug($state->name);
        });
    }

    public function cities()
    {
        return $this->hasMany(LocationCity::class);
    }
}
