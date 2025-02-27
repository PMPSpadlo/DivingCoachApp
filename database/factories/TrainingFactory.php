<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        // Find or create a club
        $club = Club::inRandomOrder()->first() ?? Club::factory()->create();

        // Get a trainer from the club or create one
        $trainer = $club->users()->wherePivot('role', 'trainer')->inRandomOrder()->first();
        if (!$trainer) {
            $trainer = User::factory()->trainer()->create();
            $club->users()->attach($trainer->id, ['role' => 'trainer', 'is_active' => true]);
        }

        // Nazwy lokalizacji treningowych - baseny w Polsce
        $trainingLocations = [
            'Basen Olimpijski Warszawianka',
            'Termy Maltańskie',
            'Aquapark Wrocław',
            'Basen AWF',
            'CRS Bielany',
            'Wodny Park Tychy',
            'Aqua Lublin',
            'Floating Arena Szczecin',
            'Aquasfera Olsztyn',
            'Park Wodny Kraków',
            'Centrum Rekreacyjno-Sportowe',
            'Miejski Ośrodek Sportu',
            'Olimpijczyk Gliwice',
        ];

        // Typy treningów
        $trainingTypes = ['general', 'technical', 'strength', 'conditioning'];

        // Generate a future or past date randomly - POPRAWIONY KOD
        $isFuture = $this->faker->boolean(60); // 60% chance of future training

        if ($isFuture) {
            $date = $this->faker->dateTimeBetween('now', '+2 months');
        } else {
            $date = $this->faker->dateTimeBetween('-2 months', 'now');
        }

        // Common training times in Poland
        $hours = [8, 10, 12, 14, 16, 17, 18, 19, 20];
        $date->setTime($this->faker->randomElement($hours), 0);

        // Notatki do treningów
        $trainingNotes = [
            'Trening skierowany na technikę wejścia do wody',
            'Praca nad odbiciem z trampoliny',
            'Ćwiczenia gibkościowe na sali i skoki z trampoliny',
            'Skoki z wieży dla zaawansowanych',
            'Przygotowanie techniczne do zawodów regionalnych',
            'Testy sprawności ogólnej i skoki podstawowe',
            'Praca nad koordynacją i skoki z trampoliny 1m',
            'Trening siłowy + skoki techniczne',
            'Przygotowanie do zawodów - skoki z programu startowego',
            'Trening skoczności + skoki do wody',
            'Ćwiczenia rozciągające i skoki z trampoliny 3m',
            'Trening ogólnorozwojowy + podstawy techniki',
            'Praca nad pozycją ciała w locie',
            'Trening synchronizacji dla par',
        ];

        return [
            'club_id' => $club->id,
            'trainer_id' => $trainer->id,
            'date' => $date,
            'location' => $this->faker->randomElement($trainingLocations),
            'type' => $this->faker->randomElement($trainingTypes),
            'notes' => $this->faker->optional(0.8)->randomElement($trainingNotes),
        ];
    }

    /**
     * Factory state for past trainings.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => $this->faker->dateTimeBetween('-6 months', '-1 day'),
            ];
        });
    }

    /**
     * Factory state for upcoming trainings.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => $this->faker->dateTimeBetween('+1 day', '+3 months'),
            ];
        });
    }

    /**
     * Factory state for technical trainings.
     */
    public function technical(): static
    {
        return $this->state(function (array $attributes) {
            $technicalNotes = [
                'Technika wejścia do wody - praca nad minimalizacją plamy',
                'Doskonalenie pozycji pionierka w skokach',
                'Praca nad rotacją w skokach przewrotnych',
                'Ćwiczenie odbicia z trampoliny i kontroli lotu',
                'Poprawianie techniki skoku auerbachowskiego',
                'Doskonalenie śrub w skokach z wieży',
                'Praca nad skokami z wyższym współczynnikiem trudności',
                'Technika skoku 305C (2,5 salta w przód z pozycji kucznej)',
                'Doskonalenie skoków synchronicznych',
            ];

            return [
                'type' => 'technical',
                'notes' => $this->faker->randomElement($technicalNotes),
            ];
        });
    }
}
