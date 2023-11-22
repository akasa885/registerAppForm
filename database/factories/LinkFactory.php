<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'link_path' => Str::random(10),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'registration_info' => $this->faker->paragraph,
            'banner' => $this->faker->imageUrl(),
            'active_from' => now(),
            'active_until' => now()->addDays(1),
            'event_date' => now()->addDays(2),
            'created_by' => null,
            'link_type' => 'pay',
            'price' => 10000,
            'has_member_limit' => false,
            'member_limit' => 0,
            'is_multiple_registrant_allowed' => false,
            'sub_member_limit' => 0,
        ];
    }

    /**
     * 
     * @return static 
     */
    public function free()
    {
        return $this->state(function (array $attributes) {
            return [
                'link_type' => 'free',
                'price' => 0,
            ];
        });
    }

    /**
     * 
     * @return static 
     */
    public function pay()
    {
        return $this->state(function (array $attributes) {
            return [
                'link_type' => 'pay',
                'price' => 10000,
            ];
        });
    }
}
