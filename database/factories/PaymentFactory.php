<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        // Get or create a club
        $club = Club::inRandomOrder()->first() ?? Club::factory()->create();

        // Get or create an athlete from this club
        $athlete = Athlete::where('club_id', $club->id)->inRandomOrder()->first();
        if (!$athlete) {
            $athlete = Athlete::factory()->create([
                'club_id' => $club->id,
            ]);
        }

        // Metody płatności popularne w Polsce
        $paymentMethods = ['cash', 'bank_transfer', 'card', 'paypal'];

        // Statusy płatności
        $statuses = ['pending', 'paid', 'overdue'];
        $status = $this->faker->randomElement($statuses);

        // Generuj kwotę w zakresie typowym dla polskich klubów
        $amount = $this->faker->randomFloat(2, 100, 450);

        // Data płatności w ciągu ostatnich 6 miesięcy - poprawiona kolejność
        $paymentDate = $this->faker->dateTimeBetween('-6 months', 'now');

        // Wygeneruj ID transakcji dla przelewu lub płatności kartą
        $paymentMethod = $this->faker->randomElement($paymentMethods);
        $transactionId = null;

        if ($paymentMethod === 'bank_transfer') {
            // Format zbliżony do polskich przelewów
            $transactionId = date('Ymd') . '/' . $this->faker->randomNumber(6);
        } elseif ($paymentMethod === 'card' || $paymentMethod === 'paypal') {
            // Format zbliżony do transakcji online
            $transactionId = strtoupper($this->faker->bothify('??####?###'));
        }

        return [
            'club_id' => $club->id,
            'athlete_id' => $athlete->id,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'transaction_id' => $transactionId,
            'currency' => 'PLN',
            'status' => $status,
            'payment_method' => $paymentMethod,
        ];
    }

    /**
     * Factory state for paid payments.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'payment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            ];
        });
    }

    /**
     * Factory state for pending payments.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'payment_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            ];
        });
    }

    /**
     * Factory state for overdue payments.
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'overdue',
                'payment_date' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
            ];
        });
    }

    /**
     * Factory state for monthly club fee payments.
     */
    public function monthlyFee(): static
    {
        return $this->state(function (array $attributes) {
            // Typical monthly fees in Polish diving clubs
            $monthlyFees = [150, 180, 200, 220, 250];

            return [
                'amount' => $this->faker->randomElement($monthlyFees),
                'payment_method' => $this->faker->randomElement(['bank_transfer', 'cash']),
                'status' => $this->faker->randomElement(['paid', 'pending', 'overdue']),
            ];
        });
    }

    /**
     * Factory state for competition fee payments.
     */
    public function competitionFee(): static
    {
        return $this->state(function (array $attributes) {
            // Competition fees for Polish diving events
            $competitionFees = [250, 300, 350, 400, 450];

            return [
                'amount' => $this->faker->randomElement($competitionFees),
                'payment_method' => $this->faker->randomElement(['bank_transfer', 'cash']),
                'status' => $this->faker->randomElement(['paid', 'pending']),
            ];
        });
    }

    /**
     * Factory state for annual membership fee.
     */
    public function annualFee(): static
    {
        return $this->state(function (array $attributes) {
            // Annual membership fees in Polish clubs
            $annualFees = [300, 350, 400, 450, 500];

            // Set payment date to January/February
            $year = date('Y');
            $month = $this->faker->randomElement([1, 2]);
            $day = $this->faker->numberBetween(1, 28);
            $paymentDate = \Carbon\Carbon::createFromDate($year, $month, $day);

            return [
                'amount' => $this->faker->randomElement($annualFees),
                'payment_method' => $this->faker->randomElement(['bank_transfer', 'cash']),
                'payment_date' => $paymentDate,
                'status' => 'paid',
            ];
        });
    }

    /**
     * Factory state for equipment purchase payments.
     */
    public function equipmentFee(): static
    {
        return $this->state(function (array $attributes) {
            // Costs of diving equipment in Poland
            $equipmentFees = [100, 150, 200, 250, 400];

            return [
                'amount' => $this->faker->randomElement($equipmentFees),
                'payment_method' => $this->faker->randomElement(['cash', 'card']),
                'status' => 'paid',
            ];
        });
    }
}
