<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = Ad::all();

        foreach ($ads as $ad) {
            $subcategory = Category::find($ad->category_id);
            $category = $subcategory->parent; // Assuming you have a 'parent' relationship set up on your Category model.

            if (!$category || !$subcategory) {
                continue;
            }

            // Replace double quotes with underscores in the subcategory name
            $sanitizedAdTitle = str_replace('"', '_', $ad->title);

            // Format the directory and file paths
            $directory = "{$category->name}/{$subcategory->name}/{$sanitizedAdTitle}";
            $firstImagePath = "{$directory}/1.jpg";
            $secondImagePath = "{$directory}/2.jpg";

            $firstSourceDirectory = public_path("demo/ads/{$firstImagePath}");
            $firstDestinationDirectory = public_path("demo/images/ads/{$firstImagePath}");

            $secondSourceDirectory = public_path("demo/ads/{$secondImagePath}");
            $secondDestinationDirectory = public_path("demo/images/ads/{$secondImagePath}");

            // Check if the first SVG file exists at the source directory.
            if (File::exists($firstSourceDirectory)) {
                // Create the first destination directory if it doesn't exist.
                File::ensureDirectoryExists(dirname($firstDestinationDirectory));

                // Copy the first SVG file to the first destination directory.
                File::copy($firstSourceDirectory, $firstDestinationDirectory);

                // Associate the copied first SVG file with the ad using Spatie Media Library.
                $ad->addMedia($firstDestinationDirectory)->toMediaCollection('ads');
            }

            // Check if the second SVG file exists at the source directory.
            if (File::exists($secondSourceDirectory)) {
                // Create the second destination directory if it doesn't exist.
                File::ensureDirectoryExists(dirname($secondDestinationDirectory));

                // Copy the second SVG file to the second destination directory.
                File::copy($secondSourceDirectory, $secondDestinationDirectory);

                // Associate the copied second SVG file with the ad using Spatie Media Library.
                $ad->addMedia($secondDestinationDirectory)->toMediaCollection('ads');
            }

        }
    }
}
