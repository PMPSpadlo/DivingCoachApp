<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

class AthleteFactory extends Factory
{
    protected $model = Athlete::class;

    public function definition(): array
    {
        // Get or create a club
        $club = Club::where('active', true)->inRandomOrder()->first();
        if (!$club) {
            $club = Club::factory()->create();
        }

        // Generate a random birthdate for athletes between 10 and 25 years old
        $birthDate = $this->faker->dateTimeBetween('-25 years', '-10 years');

        // Determine age category based on age
        $age = now()->diffInYears($birthDate);
        $ageCategory = $age < 18 ? 'junior' : 'senior';

        // Polish first and last names
        $gender = $this->faker->randomElement(['male', 'female']);

        if ($gender === 'male') {
            $firstNames = [
                'Adam', 'Piotr', 'Paweł', 'Michał', 'Krzysztof', 'Jan', 'Tomasz',
                'Marcin', 'Jakub', 'Bartosz', 'Kacper', 'Filip', 'Mateusz', 'Maciej',
                'Szymon', 'Dawid', 'Wojciech', 'Kamil', 'Mikołaj', 'Antoni'
            ];
            $lastNames = [
                'Nowak', 'Kowalski', 'Wiśniewski', 'Wójcik', 'Kowalczyk', 'Kamiński',
                'Lewandowski', 'Zieliński', 'Szymański', 'Woźniak', 'Dąbrowski',
                'Kozłowski', 'Jankowski', 'Mazur', 'Kwiatkowski', 'Krawczyk',
                'Piotrowski', 'Grabowski', 'Nowakowski', 'Pawłowski'
            ];
        } else {
            $firstNames = [
                'Anna', 'Maria', 'Katarzyna', 'Małgorzata', 'Agnieszka', 'Aleksandra',
                'Magdalena', 'Maja', 'Zofia', 'Hanna', 'Julia', 'Zuzanna', 'Natalia',
                'Oliwia', 'Wiktoria', 'Amelia', 'Lena', 'Laura', 'Alicja', 'Pola'
            ];
            $lastNames = [
                'Nowak', 'Kowalska', 'Wiśniewska', 'Wójcik', 'Kowalczyk', 'Kamińska',
                'Lewandowska', 'Zielińska', 'Szymańska', 'Woźniak', 'Dąbrowska',
                'Kozłowska', 'Jankowska', 'Mazur', 'Kwiatkowska', 'Krawczyk',
                'Piotrowska', 'Grabowska', 'Nowakowska', 'Pawłowska'
            ];
        }

        $firstName = $this->faker->randomElement($firstNames);
        $lastName = $this->faker->randomElement($lastNames);

        // Generate email for younger athletes (optional)
        $email = null;
        if ($age >= 14) {
            $email = strtolower($this->transliteratePolish($firstName) . '.' . $this->transliteratePolish($lastName) . '@' . $this->faker->safeEmailDomain());
        }

        // Polish phone number format (for older athletes or parents)
        $phonePrefix = ['45', '50', '51', '53', '60', '66', '69', '72', '73', '78', '79', '88'];
        $phoneNumber = '+48 ' . $this->faker->randomElement($phonePrefix) . $this->faker->numerify(' ### ## ##');

        // Add realistic notes for diving athletes
        $notes = $this->generateRealisticNotes($ageCategory, $age);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'phone' => $age < 16 ? 'Tel. rodzica: ' . $phoneNumber : $phoneNumber,
            'email' => $email,
            'club_id' => $club->id,
            'notes' => $notes,
            'age_category' => $ageCategory,
        ];
    }

    /**
     * Helper function to generate realistic notes for diving athletes
     */
    private function generateRealisticNotes($ageCategory, $age): ?string
    {
        // 70% chance to have notes
        if ($this->faker->boolean(70)) {
            $notesPool = [
                'junior' => [
                    'Talent do skoków z wieży.',
                    'Dobrze radzi sobie z podstawowymi skokami z trampoliny 1m.',
                    'Wymaga pracy nad techniką wejścia do wody.',
                    'Bardzo dobra gibkość, potencjał na przyszłość.',
                    'Problemy z rotacją w skokach przewrotnych.',
                    'Dobra koordynacja ruchowa, szybko się uczy.',
                    'Trenuje od 2 lat, widoczne postępy.',
                    'Strach przed skokami z większej wysokości.',
                    'Uczestniczył w zawodach wojewódzkich (3. miejsce).',
                    'Dobra technika odbicia z trampoliny.',
                    'Pracujemy nad zwiększeniem pewności siebie na wieży.',
                    'Potrzebuje poprawić kontrolę ciała w powietrzu.',
                    'Rodzice bardzo zaangażowani w rozwój.',
                    'W szkole sportowej, dobry uczeń.',
                    'Rozważa specjalizację w skokach synchronicznych.',
                ],
                'senior' => [
                    'Specjalizuje się w skokach z wieży 10m.',
                    'Wielokrotny uczestnik Mistrzostw Polski Juniorów.',
                    'Problemy z barkiem po kontuzji (2022).',
                    'Kandydat do kadry narodowej.',
                    'Dobry technicznie, pracujemy nad skokami o wyższym współczynniku trudności.',
                    'Preferuje skoki z trampoliny 3m.',
                    'Reprezentant klubu na zawodach międzynarodowych.',
                    'Zwycięzca klasyfikacji juniorów w 2023.',
                    'Trenuje od 10 lat, doświadczony zawodnik.',
                    'Przygotowuje się do skoków synchronicznych z Nowakiem.',
                    'Po przerwie treningowej (studia), wraca do formy.',
                    'Mistrz Polski Juniorów w kategorii skoki z trampoliny 3m.',
                    'Dobra technika, pracujemy nad konsekwencją.',
                    'W przygotowaniach do kwalifikacji olimpijskich.',
                    'Przeszedł z klubu w Gdańsku w 2022 roku.',
                ]
            ];

            $categoryNotes = $notesPool[$ageCategory];

            // Pick 1-3 random notes and combine them
            return implode(' ', $this->faker->randomElements($categoryNotes, $this->faker->numberBetween(1, 3)));
        }

        return null;
    }

    /**
     * Helper function to transliterate Polish characters for email generation
     */
    private function transliteratePolish($text) {
        $from = ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż'];
        $to = ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z'];

        return str_replace($from, $to, strtolower($text));
    }

    /**
     * Factory state for junior athletes.
     */
    public function junior(): static
    {
        return $this->state(function (array $attributes) {
            // Generate a birthdate for junior athletes (10-17 years old)
            $birthDate = $this->faker->dateTimeBetween('-17 years', '-10 years');

            return [
                'birth_date' => $birthDate,
                'age_category' => 'junior',
            ];
        });
    }

    /**
     * Factory state for senior athletes.
     */
    public function senior(): static
    {
        return $this->state(function (array $attributes) {
            // Generate a birthdate for senior athletes (18-25 years old)
            $birthDate = $this->faker->dateTimeBetween('-25 years', '-18 years');

            return [
                'birth_date' => $birthDate,
                'age_category' => 'senior',
            ];
        });
    }
}
