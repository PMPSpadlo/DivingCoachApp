<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\ClubUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubUserFactory extends Factory
{
    protected $model = ClubUser::class;

    public function definition(): array
    {
        // Find or create a club
        $club = Club::inRandomOrder()->first() ?? Club::factory()->create();

        // Find or create a trainer
        $user = User::where('role', 'trainer')->inRandomOrder()->first();
        if (!$user) {
            $user = User::factory()->trainer()->create();
        }

        return [
            'club_id' => $club->id,
            'user_id' => $user->id,
            'role' => 'trainer', // Default role for the pivot
            'is_active' => true,
        ];
    }

    /**
     * Factory state for club owner role.
     */
    public function clubOwner(): static
    {
        return $this->state(function (array $attributes) {
            // Get or create a club owner user
            $user = User::where('role', 'club_owner')->inRandomOrder()->first();
            if (!$user) {
                $user = User::factory()->clubOwner()->create();
            }

            return [
                'user_id' => $user->id,
                'role' => 'club_owner',
            ];
        });
    }

    /**
     * Factory state for inactive user in club.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
