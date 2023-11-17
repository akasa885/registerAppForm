<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait UuidIdenty {
    /**
     * Boot function from Laravel.
     */
    public static function bootUuidIdenty()
    {
        static::creating(function ($model) {
            try {
                $model->uuid = Str::uuid()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    /**
     * Get the route key for the model.
     */

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Get route key name.
     */
    public function getRouteKey()
    {
        return $this->attributes['uuid'];
    }
}