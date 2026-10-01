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
        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'buyer'],
            ['id' => 3, 'name' => 'seller'],
        ], ['id'], ['name']);
    }
}
