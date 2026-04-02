<?php

namespace Database\Factories;

use App\Models\TickTickHabit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TickTickHabitCheckIn>
 */
class TickTickHabitCheckInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkinTime = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'ticktick_id' => $this->faker->unique()->regexify('[a-f0-9]{24}'),
            'habit_id' => TickTickHabit::factory(),
            'checkin_stamp' => (int) $checkinTime->format('Ymd'),
            'checkin_time' => $checkinTime,
            'op_time' => $checkinTime,
            'value' => $this->faker->randomFloat(1, 0, 10),
            'goal' => $this->faker->randomFloat(1, 1, 10),
            'status' => 2,
            'synced_at' => now(),
        ];
    }
}
