<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TickTickHabit>
 */
class TickTickHabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticktick_id' => $this->faker->unique()->regexify('[a-f0-9]{24}'),
            'name' => $this->faker->words(3, true),
            'color' => $this->faker->hexColor(),
            'status' => 0,
            'type' => 'Real',
            'goal' => $this->faker->numberBetween(1, 10),
            'step' => 1,
            'unit' => 'Count',
            'repeat_rule' => 'RRULE:FREQ=DAILY;INTERVAL=1',
            'reminders' => '',
            'encouragement' => $this->faker->words(8, true),
            'synced_at' => now(),
        ];
    }

    public function archived(): static
    {
        return $this->state(['status' => 1]);
    }
}
