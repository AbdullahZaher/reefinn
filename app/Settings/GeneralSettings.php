<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $hotel_name;
    public ?string $logo;
    public string $branch_no;
    public string $phone;
    public string $commercial_register;
    public string $tax_number;
    public string $checkout_default_time;
    public int $auto_renew_after;
    public string $timezone;

    public array $reservation_lease_terms;

    public static function group(): string
    {
        return 'general';
    }
}