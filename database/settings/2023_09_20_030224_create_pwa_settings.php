<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('pwa.name', 'AdFox');
        $this->migrator->add('pwa.short_name', 'AdFox');
        $this->migrator->add('pwa.start_url', '/');
        $this->migrator->add('pwa.display', 'standalone');
        $this->migrator->add('pwa.background_color', '#ffffff');
        $this->migrator->add('pwa.theme_color', '#000000');
        $this->migrator->add('pwa.description', 'AdFox is your go-to marketplace for buying and selling goods locally and nationally. Simple, fast, and efficient. Discover great deals today!'); // Using AdFox description
        $this->migrator->add('pwa.icons', [
            [
                "src" => "/path-to-icon.png",
                "sizes" => "192x192",
                "type" => "image/png"
            ]
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('pwa.name');
        $this->migrator->delete('pwa.short_name');
        $this->migrator->delete('pwa.start_url');
        $this->migrator->delete('pwa.display');
        $this->migrator->delete('pwa.background_color');
        $this->migrator->delete('pwa.theme_color');
        $this->migrator->delete('pwa.description');
        $this->migrator->delete('pwa.icons');
    }
};
