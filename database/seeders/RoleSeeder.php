<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        foreach (['admin', 'seller', 'buyer'] as $roleName) {
            if (!DB::table('roles')->where('name', $roleName)->exists()) {
                DB::table('roles')->insert(['name' => $roleName]);
            }
        }
    }
}
