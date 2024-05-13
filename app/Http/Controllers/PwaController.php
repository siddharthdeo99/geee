<?php
namespace App\Http\Controllers;
use App\Settings\PwaSettings;

class PwaController extends Controller
{
    public function manifest(PwaSettings $settings)
    {
        return response()->json([
            "name" => $settings->name,
            "short_name" => $settings->short_name,
            "start_url" => $settings->start_url,
            "display" => $settings->display,
            "background_color" => $settings->background_color,
            "theme_color" => $settings->theme_color,
            "description" => $settings->description,
            "icons" => $settings->icons
        ])->header('Content-Type', 'application/manifest+json');
    }
}
