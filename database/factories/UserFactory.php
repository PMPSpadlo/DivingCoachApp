<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $roles = ['trainer', 'club_owner', 'super_admin'];
        $randomRole = $this->faker->randomElement($roles);

        // Polish first and last names
        $polishFirstNames = [
            'Adam', 'Piotr', 'Paweł', 'Michał', 'Krzysztof', 'Andrzej', 'Jan', 'Stanisław',
            'Tomasz', 'Marcin', 'Marek', 'Wojciech', 'Łukasz', 'Zbigniew', 'Jakub', 'Bartosz',
            'Anna', 'Maria', 'Katarzyna', 'Małgorzata', 'Agnieszka', 'Barbara', 'Ewa', 'Krystyna',
            'Magdalena', 'Elżbieta', 'Joanna', 'Aleksandra', 'Zofia', 'Monika', 'Teresa', 'Natalia'
        ];

        $polishLastNames = [
            'Nowak', 'Kowalski', 'Wiśniewski', 'Wójcik', 'Kowalczyk', 'Kamiński', 'Lewandowski',
            'Zieliński', 'Szymański', 'Woźniak', 'Dąbrowski', 'Kozłowski', 'Jankowski', 'Mazur',
            'Kwiatkowski', 'Krawczyk', 'Piotrowski', 'Grabowski', 'Nowakowski', 'Pawłowski',
            'Kowalska', 'Wiśniewska', 'Wójcik', 'Kamińska', 'Lewandowska', 'Zielińska',
            'Szymańska', 'Dąbrowska', 'Kozłowska', 'Jankowska'
        ];

        $firstName = $this->faker->randomElement($polishFirstNames);
        $lastName = $this->faker->randomElement($polishLastNames);

        // Polish cities
        $polishCities = [
            'Warszawa', 'Kraków', 'Łódź', 'Wrocław', 'Poznań', 'Gdańsk', 'Szczecin',
            'Bydgoszcz', 'Lublin', 'Białystok', 'Katowice', 'Gdynia', 'Częstochowa',
            'Radom', 'Sosnowiec', 'Toruń', 'Kielce', 'Rzeszów', 'Olsztyn', 'Bielsko-Biała'
        ];

        // Polish phone number format
        $phonePrefix = ['45', '50', '51', '53', '60', '66', '69', '72', '73', '78', '79', '88'];
        $phoneNumber = '+48 ' . $this->faker->randomElement($phonePrefix) . $this->faker->numerify(' ### ## ##');

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($this->transliteratePolish($firstName) . '.' . $this->transliteratePolish($lastName) . '@' . $this->faker->safeEmailDomain()),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // default password for testing
            'remember_token' => Str::random(10),
            'role' => $randomRole,
            'phone' => $phoneNumber,
            'address' => 'ul. ' . $this->faker->streetName() . ' ' . $this->faker->buildingNumber(),
            'city' => $this->faker->randomElement($polishCities),
            'country' => 'Polska',
            'profile_photo_path' => null,
        ];
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
     * Factory state for trainer role.
     */
    public function trainer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'trainer',
            ];
        });
    }

    /**
     * Factory state for club owner role.
     */
    public function clubOwner(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'club_owner',
            ];
        });
    }

    /**
     * Factory state for super admin role.
     */
    public function superAdmin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'super_admin',
            ];
        });
    }

    /**
     * Indicate that the user email should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
