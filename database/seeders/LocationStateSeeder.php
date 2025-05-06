<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\WilayahIndonesia;
use App\Models\LocationState;

class LocationStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wilayah = WilayahIndonesia::connect()->getProvinsi();

        foreach ($wilayah['value'] as $provinsi) {
            LocationState::create([
                'name' => $provinsi['name'],
                'id' => $provinsi['id']
            ]);
        }
    }
}
