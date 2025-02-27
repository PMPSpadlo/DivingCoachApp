<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        // Realistyczne nazwy polskich klubów skoków do wody
        $divingClubNames = [
            'AZS',
            'MUKP',
            'UKS',
            'MKS',
            'KS',
            'WKS',
            'CKiS',
            'MTP',
            'Delfin',
            'Neptun',
            'Orka',
            'Foka',
            'Wodnik',
            'Posejdon',
            'Olimp',
        ];

        // Polskie miasta z basenami odpowiednimi do skoków do wody
        $polishCities = [
            'Warszawa',
            'Poznań',
            'Wrocław',
            'Kraków',
            'Gdańsk',
            'Katowice',
            'Toruń',
            'Szczecin',
            'Olsztyn',
            'Łódź',
            'Lublin',
            'Rzeszów',
            'Bydgoszcz',
            'Opole',
            'Częstochowa'
        ];

        // Get a random club owner (or create one if none exists)
        $owner = User::where('role', 'club_owner')->inRandomOrder()->first();

        if (!$owner) {
            $owner = User::factory()->clubOwner()->create();
        }

        // Generate club name
        $clubPrefix = $this->faker->randomElement($divingClubNames);
        $clubCity = $this->faker->randomElement($polishCities);
        $clubName = '';

        // Format club name based on prefix
        switch ($clubPrefix) {
            case 'AZS':
                $clubName = "AZS $clubCity";
                break;
            case 'MUKP':
                $clubName = "MUKP $clubCity";
                break;
            case 'UKS':
                $clubName = "UKS Pływak $clubCity";
                break;
            case 'MKS':
            case 'KS':
            case 'WKS':
                $clubName = "$clubPrefix $clubCity";
                break;
            case 'CKiS':
                $clubName = "CKiS $clubCity";
                break;
            case 'MTP':
                $clubName = "MTP $clubCity";
                break;
            default:
                $clubName = "KS $clubPrefix $clubCity";
        }

        // Realistic email for the club
        $clubEmailDomain = strtolower(str_replace(' ', '', $this->transliteratePolish($clubName)));
        $clubEmail = "kontakt@" . preg_replace('/[^a-z0-9]/', '', $clubEmailDomain) . ".pl";

        // Polish phone number format
        $phonePrefix = ['22', '12', '42', '71', '61', '58', '91', '52', '81', '85', '32'];
        $phoneNumber = '+48 ' . $this->faker->randomElement($phonePrefix) . ' ' . $this->faker->numerify('### ## ##');

        return [
            'name' => $clubName,
            'address' => 'ul. ' . $this->faker->streetName() . ' ' . $this->faker->buildingNumber(),
            'city' => $clubCity,
            'country' => 'Polska',
            'phone' => $phoneNumber,
            'email' => $clubEmail,
            'description' => 'Klub specjalizujący się w skokach do wody dla dzieci, młodzieży i dorosłych. '
                . 'Prowadzimy treningi na wszystkich poziomach zaawansowania pod okiem '
                . 'wykwalifikowanych trenerów.',
            'website' => 'https://www.' . preg_replace('/[^a-z0-9]/', '', $clubEmailDomain) . '.pl',
            'active' => true,
            'owner_id' => $owner->id,
        ];
    }

    /**
     * Helper function to transliterate Polish characters
     */
    private function transliteratePolish($text) {
        $from = ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż', ' '];
        $to = ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z', ''];

        return str_replace($from, $to, $text);
    }

    /**
     * Factory state for inactive clubs.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
}
