<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\CompetitionResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionResultFactory extends Factory
{
    protected $model = CompetitionResult::class;

    public function definition(): array
    {
        // Get or create a competition
        $competition = Competition::where('date', '<', now())->inRandomOrder()->first();
        if (!$competition) {
            $competition = Competition::factory()->past()->create();
        }

        // Get or create an athlete from the same club as the competition
        $athlete = Athlete::where('club_id', $competition->club_id)->inRandomOrder()->first();
        if (!$athlete) {
            $athlete = Athlete::factory()->create([
                'club_id' => $competition->club_id,
            ]);
        }

        // Kody skoków wg FINA
        $diveCodes = [
            // Forward dives (1xx)
            '101', '102', '103', '104', '105', '106', '107', '109',
            // Back dives (2xx)
            '201', '202', '203', '204', '205', '206', '207', '209',
            // Reverse dives (3xx)
            '301', '302', '303', '304', '305', '306', '307', '309',
            // Inward dives (4xx)
            '401', '402', '403', '404', '405', '406', '407', '409',
            // Twisting dives (5xx)
            '5132', '5134', '5136', '5152', '5154', '5156',
            // Armstand dives (6xx) - platform only
            '612', '614', '624', '626', '636', '638',
        ];

        // Pozycje skoków
        $positions = ['A', 'B', 'C', 'D'];

        // Generate a dive type
        $diveCode = $this->faker->randomElement($diveCodes);
        $position = $this->faker->randomElement($positions);
        $diveType = $diveCode . $position;

        // Assign difficulty level (DD - Degree of Difficulty) based on realistic values
        $difficultyLevel = $this->getDifficultyLevel($diveCode, $position);

        // Generate a random score based on the competition level and athlete age
        // Higher scores for national/international competitions
        $competitionLevelModifier = ($competition->level === 'national') ? 0.5 :
            (($competition->level === 'international') ? 1.0 : 0);

        // Base score calculation
        $individualScores = [];
        for ($i = 0; $i < 7; $i++) {
            // Base range from 5.0 to 8.5
            $baseScore = $this->faker->randomFloat(1, 5.0, 8.5);
            // Add modifiers
            $modifiedScore = min(10.0, $baseScore + $competitionLevelModifier);
            $individualScores[] = $modifiedScore;
        }

        sort($individualScores);
        // Remove highest and lowest
        array_shift($individualScores);
        array_pop($individualScores);
        // Average of the 5 remaining scores
        $avgScore = array_sum($individualScores) / 5;
        // Final score: avg x 3 x difficulty
        $score = round($avgScore * 3 * (float)$difficultyLevel, 2);

        // Random rank between 1 and 12
        $rank = $this->faker->numberBetween(1, 12);

        // Realistic notes about the dive performance
        $notes = $this->generatePerformanceNote($avgScore);

        return [
            'competition_id' => $competition->id,
            'athlete_id' => $athlete->id,
            'score' => $score,
            'dive_type' => $diveType,
            'difficulty_level' => $difficultyLevel,
            'rank' => $rank,
            'notes' => $notes,
        ];
    }

    /**
     * Helper function to get realistic difficulty level based on dive code and position
     */
    private function getDifficultyLevel($diveCode, $position): string
    {
        // Simplified difficulty calculation
        // In reality, difficulty is determined by a complex combination of factors
        $dd = 0.0;

        // Base difficulty from first digit (dive group)
        $group = (int)substr($diveCode, 0, 1);
        $dd += ($group == 6) ? 0.5 : 0.3; // Armstand dives are harder

        // Number of rotations (from second+third digits)
        $rotations = (int)substr($diveCode, 1, 2);
        $dd += $rotations * 0.4;

        // Twisting dives (5xxx) have higher difficulty
        if ($group == 5) {
            $dd += 0.6;
        }

        // Position modifier
        switch ($position) {
            case 'A': // Straight - hardest
                $dd += 0.4;
                break;
            case 'B': // Pike
                $dd += 0.3;
                break;
            case 'C': // Tuck - easiest
                $dd += 0.2;
                break;
            case 'D': // Free
                $dd += 0.3;
                break;
        }

        // Ensure DD is within realistic range
        $dd = max(1.2, min(4.1, $dd));

        return number_format($dd, 1);
    }

    /**
     * Helper function to generate realistic performance notes
     */
    private function generatePerformanceNote($avgScore): ?string
    {
        // 60% chance to have notes
        if (!$this->faker->boolean(60)) {
            return null;
        }

        if ($avgScore >= 8.5) {
            $excellentNotes = [
                'Perfekcyjne wejście do wody, minimalna plama.',
                'Świetnie wykonany skok, równe nogi, pełna kontrola.',
                'Doskonała pozycja ciała w locie, idealne wyprostowanie.',
                'Bardzo precyzyjny skok, znakomita technika wykonania.',
                'Wzorowa prezentacja, brak widocznych błędów.',
                'Idealnie wykonane śruby, pełna kontrola obrotu.',
                'Doskonałe wejście pionowe, brak rozbryzgów.',
            ];
            return $this->faker->randomElement($excellentNotes);
        } elseif ($avgScore >= 7.0) {
            $goodNotes = [
                'Dobre wejście do wody, minimalne bryzgi.',
                'Poprawna technika, niewielkie uwagi do pozycji nóg.',
                'Dobra wysokość, lekkie niedociągnięcia przy wejściu.',
                'Solidny skok, drobne problemy z wyprostowaniem.',
                'Prawidłowa rotacja, niewielkie odchylenie przy wejściu.',
                'Dobra prezentacja, drobne niedociągnięcia techniczne.',
            ];
            return $this->faker->randomElement($goodNotes);
        } elseif ($avgScore >= 5.5) {
            $averageNotes = [
                'Widoczny brak kontroli przy wejściu, duża plama.',
                'Niepełna pozycja w locie, słabe wyprostowanie.',
                'Zbyt wczesne otwarcie z saltach, utrata wysokości.',
                'Problemy z kontrolą rotacji, przekręcony skok.',
                'Średnia wysokość, złamane kolana przy wejściu.',
                'Widoczny brak pewności w wykonaniu.',
            ];
            return $this->faker->randomElement($averageNotes);
        } else {
            $poorNotes = [
                'Bardzo duża plama przy wejściu, brak kontroli.',
                'Nieukończona rotacja, wejście pod kątem.',
                'Znaczne problemy techniczne, zgięte nogi i stopy.',
                'Słaba wysokość, brak kontroli pozycji.',
                'Zagubienie w pozycji, nieprawidłowy rytm.',
                'Poważne problemy z wejściem, upadek na plecy.',
            ];
            return $this->faker->randomElement($poorNotes);
        }
    }

    /**
     * Factory state for high scores.
     */
    public function highScore(): static
    {
        return $this->state(function (array $attributes) {
            // High difficulty dive with excellent execution
            $difficultyLevel = number_format($this->faker->randomFloat(1, 3.0, 4.1), 1);

            // Calculate a high score
            $avgJudgeScore = $this->faker->randomFloat(2, 8.5, 10.0);
            $score = round($avgJudgeScore * 3 * (float)$difficultyLevel, 2);

            $excellentNotes = [
                'Perfekcyjne wejście do wody, brak widocznych bryzgów.',
                'Wzorowa technika, pełna kontrola przez cały czas trwania skoku.',
                'Doskonała wysokość i precyzja wykonania.',
                'Idealnie wyprostowane nogi, stopy złączone.',
                'Bezbłędna rotacja i pozycja ciała w każdej fazie.',
            ];

            return [
                'score' => $score,
                'difficulty_level' => $difficultyLevel,
                'rank' => $this->faker->numberBetween(1, 3),
                'notes' => $this->faker->randomElement($excellentNotes),
            ];
        });
    }

    /**
     * Factory state for low scores.
     */
    public function lowScore(): static
    {
        return $this->state(function (array $attributes) {
            // Calculate a low score
            $difficultyLevel = number_format($this->faker->randomFloat(1, 1.2, 2.5), 1);
            $avgJudgeScore = $this->faker->randomFloat(2, 4.0, 6.5);
            $score = round($avgJudgeScore * 3 * (float)$difficultyLevel, 2);

            $poorNotes = [
                'Znaczna plama przy wejściu, utrata kontroli.',
                'Nieukończona rotacja, wpadnięcie do wody pod kątem.',
                'Zgięte nogi przez cały czas trwania skoku.',
                'Bardzo niska wysokość, brak kontroli pozycji.',
                'Rozdzielone stopy, ugięte kolana przy wejściu.',
                'Przechylenie w bok podczas całego skoku.',
            ];

            return [
                'score' => $score,
                'difficulty_level' => $difficultyLevel,
                'rank' => $this->faker->numberBetween(8, 12),
                'notes' => $this->faker->randomElement($poorNotes),
            ];
        });
    }
}
