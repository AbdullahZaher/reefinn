<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.hotel_name', config('app.name'));
        $this->migrator->add('general.logo', null);
        $this->migrator->add('general.branch_no', "4546521");
        $this->migrator->add('general.phone', "012345678910");
        $this->migrator->add('general.commercial_register', "MT45132621");
        $this->migrator->add('general.tax_number', "132165341352");
        $this->migrator->add('general.checkout_default_time', "13:00");

        $this->migrator->add('general.reservation_lease_terms', [
            1 => [
                'en' => 'Daily Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, vitae aliquam',
                'ar' => 'يومي لوريم ايبسوم دولار سيت اميت، كونسيكتيتور أديبيسسينج إليت. نولا أويسمود، نيسليجيت أليكوام أولتريسيس، نونك نيسل أليكويت نونك، فيتاي أليكوام'
            ],
            2 => [
                'en' => 'Weekly Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, vitae aliquam',
                'ar' => 'اسبوعي لوريم ايبسوم دولار سيت اميت، كونسيكتيتور أديبيسسينج إليت. نولا أويسمود، نيسليجيت أليكوام أولتريسيس، نونك نيسل أليكويت نونك، فيتاي أليكوام'
            ],
            3 => [
                'en' => 'Monthly Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, vitae aliquam',
                'ar' => 'شهري لوريم ايبسوم دولار سيت اميت، كونسيكتيتور أديبيسسينج إليت. نولا أويسمود، نيسليجيت أليكوام أولتريسيس، نونك نيسل أليكويت نونك، فيتاي أليكوام'
            ],
        ]);
    }
};