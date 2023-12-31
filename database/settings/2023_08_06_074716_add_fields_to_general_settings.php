<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.auto_renew_after', 4);
        $this->migrator->add('general.timezone', 'UTC');
    }
};