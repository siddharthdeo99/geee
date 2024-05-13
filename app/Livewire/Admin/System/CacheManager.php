<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Artisan;
use File;

class CacheManager extends Component
{
    /**
     * Clear the application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            notifySuccess('System cache cleared successfully.');
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Clear all compiled view files.
     */
    public function clearViews()
    {
        try {
            Artisan::call('view:clear');
            notifySuccess('Compiled views cache cleared successfully.');
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Clear all application log files.
     */
    public function clearLogs()
    {
        try {
            $files = File::allFiles(storage_path('logs'));
            foreach ($files as $file) {
                File::delete($file);
            }
            notifySuccess('Log files cleared successfully.');
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.system.cache-manager');
    }
}
