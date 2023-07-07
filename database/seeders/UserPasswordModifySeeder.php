<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserPasswordModifySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // foreach User::all change its password to '12345678'
        User::all()->each(function ($user) {
            $user->password = Hash::make('12345678');
            $user->save();
        });
    }
}
