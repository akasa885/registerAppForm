<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WilayahIndonesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LocationStateSeeder::class,
            LocationCitySeeder::class
        ]);
    }
}
