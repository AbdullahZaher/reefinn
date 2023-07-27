<?php

namespace Database\Seeders;

use App\Enums\ApartmentStateEnum;
use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $i = 0;

        Apartment::factory(
            [
                'name' => function () use (&$i) {
                    return 100 + $i++;
                },
                'state' => 1,
            ]
        )->count(20)->create();

        Apartment::all()->each(
            fn (Apartment $apartment) => $apartment->reservations()->saveMany(
                Reservation::factory(5)->make()
            )
        );
    }
}