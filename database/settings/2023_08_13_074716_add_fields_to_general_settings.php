<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.location', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.183949017486!2d-73.98811752275478!3d40.757978734797526!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2seg!4v1691770924510!5m2!1sen!2seg');
    }
};