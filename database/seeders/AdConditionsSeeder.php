<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AdConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ad_conditions')->insert([
            ['name' => 'New'],
            ['name' => 'Used - Like New'],
            ['name' => 'Used - Good'],
            ['name' => 'Used - Fair'],
        ]);
    }
}
