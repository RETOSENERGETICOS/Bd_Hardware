<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            0 => [
                'name' => 'Administrador'
            ],
            1 => [
                'name' => 'Gestor'
            ],
            2 => [
                'name' => 'Usuario'
            ],
        ]);
    }
}
