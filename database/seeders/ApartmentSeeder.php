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
        $states = collect(ApartmentStateEnum::toArray())->except(ApartmentStateEnum::Maintenance->value)->pluck('key');
        $i = 0;

        Apartment::factory([
            'name' => function () use (&$i) {
                return 100 + $i++;
            },
            'state' => fn () => $states->random(),
        ])->count(26)->create();

        Apartment::where('state', ApartmentStateEnum::Reserved->value)->get()->each(function (Apartment $apartment) {
            Reservation::factory(random_int(1, 5))->create([
                'apartment_id' => $apartment->id,
            ]);
        });

        Apartment::where('state', ApartmentStateEnum::Inhabited->value)->get()->each(function (Apartment $apartment) {
            $reservation = Reservation::factory()->create([
                'apartment_id' => $apartment->id,
                'checkin' => now()->subDays(random_int(1, 30))->format('Y-m-d'),
                'checkout' => now()->addDays(random_int(1, 30))->format('Y-m-d'),
            ]);

            $apartment->update([
                'current_reservation_id' => $reservation->id,
            ]);

            Reservation::factory(random_int(0, 2))->create([
                'apartment_id' => $apartment->id,
            ]);
        });

        Apartment::where('state', ApartmentStateEnum::Cleaning->value)->get()->each(function (Apartment $apartment) {
            Reservation::factory(random_int(0, 3))->create([
                'apartment_id' => $apartment->id,
            ]);
        });
    }
}