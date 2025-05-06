<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\WilayahIndonesia;
use App\Models\LocationCity;
use App\Models\LocationState;
use Illuminate\Support\Str;

class LocationCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = LocationState::all();

        foreach ($states as $state) {
            $wilayah = WilayahIndonesia::connect()->getCity($state->id);

            foreach ($wilayah['value'] as $city) {
                LocationCity::UpdateOrCreate([
                    'id' => $this->checkIdHasDot($city['id'])
                ],[
                    'name' => $city['name'],
                    'state_id' => $state->id
                ]);
            }
        }
    }

    private function checkIdHasDot($id)
    {
        // if yes, then remove the dot then return last value
        if (Str::contains($id, '.')) {
            $id = explode('.', $id);
            //then merge both without dot, then change to integer
            $id = $id[0] . $id[1];
            
            return (int) $id;

        }

        return $id;
    }
}
