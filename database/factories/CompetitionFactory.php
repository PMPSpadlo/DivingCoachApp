<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        // Get or create a club
        $club = Club::inRandomOrder()->first() ?? Club::factory()->create();

        // Polskie nazwy zawodów skoków do wody
        $competitionPrefixes = [
            'Mistrzostwa Polski',
            'Puchar Polski',
            'Grand Prix Polski',
            'Ogólnopolska Olimpiada Młodzieży',
            'Mistrzostwa Województwa',
            'Memoriał',
            'Otwarte Mistrzostwa',
            'Zawody Międzynarodowe',
            'Turniej',
            'Liga Młodzieżowa',
        ];

        // Kategorie
        $categories = [
            'Juniorów',
            'Seniorów',
            'Młodzików',
            'Juniorów Młodszych',
            'Open',
            'Dzieci',
            'Masters',
        ];

        // Znane polskie baseny z wieżami i trampolinami do skoków
        $divingLocations = [
            'Termy Maltańskie, Poznań',
            'Aqua Lublin, Lublin',
            'Centrum Sportowo-Rekreacyjne Warszawianka, Warszawa',
            'Akademia Wychowania Fizycznego, Warszawa',
            'Akademia Wychowania Fizycznego, Kraków',
            'Arena Gliwice, Gliwice',
            'Olimpijczyk, Gliwice',
            'CRS Bielany, Warszawa',
            'Floating Arena, Szczecin',
            'Aquasfera, Olsztyn',
        ];

        // Competition types (rodzaje skoków)
        $types = ['platform', 'springboard', 'synchro'];

        // Competition levels (poziomy zawodów)
        $levels = ['regional', 'national', 'international'];

        // Panel sędziowski
        $judgePanels = [
            'Panel sędziowski PZP',
            'Sędziowie międzynarodowi FINA',
            'Panel sędziowski wojewódzki',
            'Komisja sędziowska PZP',
            'Sędziowie klasy mistrzowskiej',
        ];

        // Generate a future or past date randomly
        $isFuture = $this->faker->boolean(60); // 60% chance of future competition

        // Poprawiony kod generowania daty - parametry w odpowiedniej kolejności
        if ($isFuture) {
            $date = $this->faker->dateTimeBetween('now', '+6 months');
        } else {
            $date = $this->faker->dateTimeBetween('-6 months', 'now');
        }

        // Nazwiska znanych polskich trenerów skoków do wody dla memoriałów
        $famousMentors = [
            'Andrzeja Kozdrańskiego',
            'Grzegorza Kowalskiego',
            'Jana Wiśniewskiego',
            'Marii Dąbrowskiej',
            'Romana Szewczyka',
        ];

        // Create competition name
        $prefix = $this->faker->randomElement($competitionPrefixes);
        $isMemorial = ($prefix === 'Memoriał');

        if ($isMemorial) {
            $name = "Memoriał im. " . $this->faker->randomElement($famousMentors) . " w Skokach do Wody";
        } else {
            $category = $this->faker->randomElement($categories);
            $name = "$prefix $category w Skokach do Wody";

            // Sometimes add year
            if ($this->faker->boolean(70)) {
                $name .= " " . date('Y');
            }
        }

        // Determine level based on competition prefix
        $level = 'regional';
        if (in_array($prefix, ['Mistrzostwa Polski', 'Puchar Polski', 'Grand Prix Polski'])) {
            $level = 'national';
        } elseif (in_array($prefix, ['Zawody Międzynarodowe', 'Memoriał']) && $this->faker->boolean(70)) {
            $level = 'international';
        }

        return [
            'name' => $name,
            'location' => $this->faker->randomElement($divingLocations),
            'date' => $date,
            'club_id' => $club->id,
            'judge_panel' => $this->faker->randomElement($judgePanels),
            'type' => $this->faker->randomElement($types),
            'level' => $level,
        ];
    }

    /**
     * Factory state for past competitions.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            ];
        });
    }

    /**
     * Factory state for upcoming competitions.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            ];
        });
    }

    /**
     * Factory state for platform competitions.
     */
    public function platform(): static
    {
        return $this->state(function (array $attributes) {
            $categories = ['Juniorów', 'Seniorów', 'Open'];
            $category = $this->faker->randomElement($categories);

            return [
                'type' => 'platform',
                'name' => "Mistrzostwa Polski $category w Skokach z Wieży " . date('Y'),
            ];
        });
    }

    /**
     * Factory state for springboard competitions.
     */
    public function springboard(): static
    {
        return $this->state(function (array $attributes) {
            $categories = ['Juniorów', 'Seniorów', 'Open', 'Młodzików'];
            $category = $this->faker->randomElement($categories);
            $height = $this->faker->randomElement(['1m', '3m']);

            return [
                'type' => 'springboard',
                'name' => "Puchar Polski $category w Skokach z Trampoliny $height " . date('Y'),
            ];
        });
    }

    /**
     * Factory state for synchronized diving competitions.
     */
    public function synchro(): static
    {
        return $this->state(function (array $attributes) {
            $categories = ['Juniorów', 'Seniorów', 'Open'];
            $category = $this->faker->randomElement($categories);
            $apparatus = $this->faker->randomElement(['Wieży', 'Trampoliny 3m']);

            return [
                'type' => 'synchro',
                'name' => "Mistrzostwa Polski $category w Skokach Synchronicznych z $apparatus " . date('Y'),
            ];
        });
    }

    /**
     * Factory state for international competitions.
     */
    public function international(): static
    {
        return $this->state(function (array $attributes) {
            $intlNames = [
                "Grand Prix Polski w Skokach do Wody - Puchar Czterech Narodów",
                "Międzynarodowy Memoriał im. Andrzeja Kozdrańskiego",
                "FINA Diving World Series - Warsaw",
                "Central European Diving Cup",
                "International Youth Diving Meet - Poland",
            ];

            return [
                'level' => 'international',
                'name' => $this->faker->randomElement($intlNames) . " " . date('Y'),
                'judge_panel' => 'Sędziowie międzynarodowi FINA',
            ];
        });
    }
}
