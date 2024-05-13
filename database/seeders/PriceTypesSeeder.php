<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('price_types')->insert([
            ['name' => 'Specify Price - Enter a specific price for the item or service.'],
            ['name' => 'Free - The item or service is being offered for free.'],
            ['name' => 'Contact for Pricing - Get in touch with the seller for pricing details.'],
            ['name' => 'Swap/Trade - Open to swapping or trading for other items.']
        ]);        
    }
}
