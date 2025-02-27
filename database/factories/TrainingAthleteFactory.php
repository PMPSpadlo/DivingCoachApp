<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Training;
use App\Models\TrainingAthlete;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingAthleteFactory extends Factory
{
    protected $model = TrainingAthlete::class;

    public function definition(): array
    {
        // Get or create a training
        $training = Training::inRandomOrder()->first();
        if (!$training) {
            $training = Training::factory()->create();
        }

        // Get or create an athlete from the same club as the training
        $athlete = Athlete::where('club_id', $training->club_id)->inRandomOrder()->first();
        if (!$athlete) {
            $athlete = Athlete::factory()->create([
                'club_id' => $training->club_id,
            ]);
        }

        return [
            'training_id' => $training->id,
            'athlete_id' => $athlete->id,
        ];
    }
}
