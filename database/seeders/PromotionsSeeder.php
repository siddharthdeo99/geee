<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $promotions = [
            [
                'name' => 'Featured Ad',
                'description' => 'Highlight your ad to get more attention',
                'price' => 18.00,
                'image' => 'featured-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'name' => 'Spotlight Ad',
                'description' => 'Show your ad in a special spotlight section',
                'price' => 25.00,
                'image' => 'spotlight-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'name' => 'Urgent Ad',
                'description' => 'Mark your ad as urgent to sell quickly',
                'price' => 10.00,
                'image' => 'urgent-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'name' => 'Website URL',
                'description' => 'Link your ad to your website',
                'price' => 20.00,
                'image' => 'website-url.svg',
                'duration' => 30, // 7 days
            ],
        ];

        // Insert promotions into the database
        DB::table('promotions')->insert($promotions);
    }
}
