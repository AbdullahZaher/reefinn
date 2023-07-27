<?php

namespace Database\Factories;

use App\Enums\ReservationStateEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkin = $this->faker->dateTimeBetween('now', '+1 week');
        $checkout = $this->faker->dateTimeBetween('+1 week', '+6 week');
        $number_of_nights = $checkout->diff($checkin)->format("%a");
        $price_for_night = random_int(100, 1000);

        return [
            'checkin' => $checkin->format('Y-m-d'),
            'checkout' => $checkout->format('Y-m-d'),
            'price_for_night' => $price_for_night,
            'total_price' => $price_for_night * $number_of_nights,
            'state' => $checkout > now() ? ReservationStateEnum::Pending->value : ReservationStateEnum::Checkout->value,
            'guest_name' => $this->faker->name,
            'guest_id' => random_int(1000000, 9999999),
            'copy' => random_int(1, 10),
            'guest_phone' => $this->faker->phoneNumber,
            'guest_birthday' => $this->faker->date('Y-m-d'),
            'number_of_companions' => random_int(0, 10),
        ];
    }
}
