<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Language;
use App\Models\SettingsProperty;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
/**
 * Fetch the active languages from the database.
 *
 * This function retrieves languages that are marked as visible from the database.
 * To improve performance, the results are cached indefinitely unless the cache
 * is explicitly cleared.
 *
 * @param bool $clear_cache If set to true, the cache for active languages will be cleared.
 * @return \Illuminate\Support\Collection A collection of active languages.
 */
function fetch_active_languages($clear_cache = false)
{
    // Define the cache key used to store the active languages.
    $cache_key = 'active_languages';

    // If clear_cache is true, remove the cached active languages.
    if ($clear_cache) {
        Cache::forget($cache_key);
    }

    // Use the cache system to store/retrieve the active languages. If the languages aren't
    // already cached, fetch them from the database and cache the result indefinitely.
    return Cache::rememberForever($cache_key, function () {
        return Language::where('is_visible', true)->orderBy('title')->get();
    });
}

/**
 * Check if the setup is complete based on the existence of the setup route file.
 *
 * @return bool
 */
function isSetupComplete(): bool
{
    $path = storage_path('installed');
    return File::exists($path);
}

/**
 * Generate a unique uppercase string.
 *
 * @param int $length The desired length of the generated string.
 * @return string The generated unique string.
 */
function uid($length = 20): string
{
    // Generate random bytes
    $bytes = random_bytes($length);

    // Convert bytes to hexadecimal
    $random = bin2hex($bytes);

    // Return the string in uppercase and trimmed to the desired length
    return strtoupper(substr($random, 0, $length));
}

/**
 * Fetches the media URL for a given setting key and media collection name.
 * If the media doesn't exist, it returns a default URL.
 *
 * @param string $settingKey The key for the setting (e.g., 'general.logo_path').
 * @param string $mediaCollectionName The name of the media collection (e.g., 'logo').
 * @param string $defaultUrl The default URL to return if the media doesn't exist.
 * @return string The media URL or the default URL.
 */
function getSettingMediaUrl(string $settingKey, string $mediaCollectionName, string $defaultUrl): string
{
    $settingsProperty = SettingsProperty::getInstance($settingKey);

    if ($settingsProperty) {
        $media = $settingsProperty->getFirstMedia($mediaCollectionName);

        if ($media) {
            return $media->getUrl();
        }
    }

    return $defaultUrl;
}


/**
 * Convert to number
 *
 * @param mixed $value
 * @return mixed
 */
function convertToNumber($value) {
    if (is_numeric($value)) {
        $int   = (int)$value;
        $float = (float)$value;

        $value = ($int == $float) ? $int : $float;
        return $value;
    } else {
        return $value;
    }
}


/**
 * Check if an addon is installed.
 *
 * @param string $moduleName The name of the module.
 * @return bool
 */
function isAddonInstalled($moduleName)
{
    $installedFilePath = base_path('app-modules/' . $moduleName . '/installed');

    return File::exists($installedFilePath);
}

/**
 * Updates or adds a key-value pair in the .env file. If the key already exists,
 * its value is updated; if not, the key-value pair is added to the end of the file.
 *
 * This function handles proper formatting of the .env file, ensuring the values
 * are correctly escaped and placed. It takes into account the different line
 * endings between Windows and other operating systems.
 *
 * @param string $envKey The environment variable key to be set or updated.
 * @param string $envValue The value to be assigned to the environment variable.
 * @return bool Returns true if the operation was successful, false otherwise.
 * @throws \Exception if there is an error during file handling.
 */
function setEnvironmentValue($envKey, $envValue)
{
    try {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        if ($keyPosition) {
            if (PHP_OS_FAMILY === 'Windows') {
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
            } else {
                $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
            }
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"";
            if ($oldLine != $newLine) {
                $str = str_replace($oldLine, $newLine, $str);
                $str = substr($str, 0, -1);
                $fp = fopen($envFile, 'w');
                fwrite($fp, $str);
                fclose($fp);
            }
        } elseif (strtoupper($envKey) == $envKey) {
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"\n";
            $str .= $newLine;
            $str = substr($str, 0, -1);
            $fp = fopen($envFile, 'w');
            fwrite($fp, $str);
            fclose($fp);
        }
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

if (!function_exists('getCustomerCurrentBuildVersion')) {
    function getCustomerCurrentBuildVersion()
    {
        return config('app.build_version', 1);
    }
}

/**
 * Show success notification.
 *
 * @param string $message The success message to be displayed.
 * @return void
 */
function notifySuccess(string $message)
{
    Filament\Notifications\Notification::make()
        ->title($message)
        ->success()
        ->send();
}

/**
 * Show error notification.
 *
 * @param string $message The error message to be displayed.
 * @return void
 */
function notifyError(string $message)
{
    Filament\Notifications\Notification::make()
        ->title($message)
        ->danger()
        ->send();
}

/**
 * Convert a given string to a URL-friendly slug.
 *
 * This function takes a string and converts it into a slug by
 * replacing spaces with hyphens and converting it to lowercase.
 *
 * @param string $string The string to be slugified.
 * @return string The slugified version of the string.
 */
function slugify($string)
{
    // Replace spaces and other special characters with hyphens
    $slug = preg_replace('/[^\w]+/', '-', $string);

    // Convert to lowercase
    $slug = strtolower($slug);

    // Trim hyphens from the beginning and end of the slug
    $slug = trim($slug, '-');

    return $slug;
}


/**
 * Generate a URL for the ad-category or location-category route with optional query string.
 *
 * @param  \App\Models\Category  $category
 * @param  \App\Models\Subcategory|null  $subcategory
 * @param  string|null  $location
 * @param  string|null  $queryString
 * @return string
 */
function generate_category_url($category, $subcategory = null, $location = null)
{

    // Generate the URL based on whether location is provided
    if ($location) {
        return route('location-category', [
            'location' => $location,
            'category' => $category->slug,
            'subcategory' => optional($subcategory)->slug
        ]);
    }

    return route('ad-category', [
        'category' => $category->slug,
        'subcategory' => optional($subcategory)->slug
    ]);
}
