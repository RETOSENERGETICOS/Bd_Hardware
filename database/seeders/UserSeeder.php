<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            0 => [
                'role_id' => 1,
                'email' => 'admin@correo.com',
                'password' => Hash::make('admin123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            1 => [
                'role_id' => 2,
                'email' => 'gestor@correo.com',
                'password' => Hash::make('gestor123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            2 => [
                'role_id' => 3,
                'email' => 'user@correo.com',
                'password' => Hash::make('user123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
