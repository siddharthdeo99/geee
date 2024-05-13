<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UsersSeeder::class,
            CategoriesSeeder::class,
            AdsSeeder::class,
            FieldsSeeder::class,
            AdPromotionsSeeder::class,
            FavouriteAdsSeeder::class
        ]);
    }
}
