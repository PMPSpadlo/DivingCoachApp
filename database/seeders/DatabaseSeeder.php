<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\ClubUser;
use App\Models\Competition;
use App\Models\CompetitionResult;
use App\Models\Payment;
use App\Models\Training;
use App\Models\TrainingAthlete;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Rozpoczynam generowanie danych testowych...');

        // Truncate all tables to start fresh
        $this->truncateTables();

        // Create a super admin
        $this->command->info('Tworzenie konta administratora...');
        $admin = User::factory()->superAdmin()->create([
            'email' => 'admin@example.com',
            'first_name' => 'Adam',
            'last_name' => 'Kowalski',
            'password' => Hash::make('password'),
            'phone' => '+48 500 100 200',
            'city' => 'Warszawa',
        ]);

        // Create 5 club owners
        $this->command->info('Tworzenie właścicieli klubów...');
        $clubOwners = User::factory(5)->clubOwner()->create();

        // Create 10 trainers
        $this->command->info('Tworzenie trenerów...');
        $trainers = User::factory(10)->trainer()->create();

        // Create 5 clubs
        $this->command->info('Tworzenie klubów...');
        $clubs = [];
        foreach ($clubOwners as $owner) {
            $clubs[] = Club::factory()->create([
                'owner_id' => $owner->id
            ]);
        }

        // Assign trainers to clubs (2-3 trainers per club)
        $this->command->info('Przypisywanie trenerów do klubów...');
        foreach ($clubs as $club) {
            // Associate the club owner
            $owner = User::find($club->owner_id);
            $club->users()->attach($owner->id, ['role' => 'club_owner', 'is_active' => true]);

            // Get 2-3 random trainers
            $clubTrainers = $trainers->random(rand(2, 3));

            foreach ($clubTrainers as $trainer) {
                $club->users()->attach($trainer->id, ['role' => 'trainer', 'is_active' => true]);
            }

            $this->command->info("Dodano trenerów do klubu: {$club->name}");
        }

        // Create athletes for each club
        $this->command->info('Tworzenie zawodników dla każdego klubu...');
        foreach ($clubs as $club) {
            // Create junior athletes (10-15 per club)
            $juniorCount = rand(10, 15);
            $juniors = Athlete::factory($juniorCount)->junior()->create(['club_id' => $club->id]);

            // Create senior athletes (5-10 per club)
            $seniorCount = rand(5, 10);
            $seniors = Athlete::factory($seniorCount)->senior()->create(['club_id' => $club->id]);

            $this->command->info("Dodano {$juniorCount} juniorów i {$seniorCount} seniorów do klubu: {$club->name}");
        }

        // Create competitions for each club
        $this->command->info('Tworzenie zawodów...');
        foreach ($clubs as $club) {
            // Past competitions (3-5 per club)
            $pastCompetitions = Competition::factory(rand(3, 5))->past()->create(['club_id' => $club->id]);

            // Future competitions (1-3 per club)
            $futureCompetitions = Competition::factory(rand(1, 3))->upcoming()->create(['club_id' => $club->id]);

            // Add specific competition types
            Competition::factory()->past()->platform()->create(['club_id' => $club->id]);
            Competition::factory()->past()->springboard()->create(['club_id' => $club->id]);
            Competition::factory()->upcoming()->synchro()->create(['club_id' => $club->id]);

            // International competitions (30% chance)
            if (rand(1, 100) <= 30) {
                Competition::factory()->international()->create(['club_id' => $club->id]);
            }

            $this->command->info("Dodano zawody dla klubu: {$club->name}");
        }

        // Create competition results
        $this->command->info('Tworzenie wyników zawodów...');
        $pastCompetitions = Competition::where('date', '<', now())->get();
        foreach ($pastCompetitions as $competition) {
            // Get athletes from the club organizing the competition
            $clubAthletes = Athlete::where('club_id', $competition->club_id)->get();

            // Also get some athletes from other clubs (for realism)
            $otherAthletes = Athlete::where('club_id', '!=', $competition->club_id)
                ->inRandomOrder()
                ->limit(rand(5, 10))
                ->get();

            $allAthletes = $clubAthletes->merge($otherAthletes);

            // Create results for a subset of athletes
            $participatingAthletes = $allAthletes->random(min(count($allAthletes), rand(8, 15)));

            // Assign ranks 1, 2, 3, etc.
            $ranks = range(1, count($participatingAthletes));
            shuffle($ranks);

            foreach ($participatingAthletes as $index => $athlete) {
                $rank = $ranks[$index];

                // Higher-ranked athletes get higher scores
                if ($rank <= 3) {
                    CompetitionResult::factory()->highScore()->create([
                        'competition_id' => $competition->id,
                        'athlete_id' => $athlete->id,
                        'rank' => $rank
                    ]);
                } elseif ($rank >= count($participatingAthletes) - 2) {
                    CompetitionResult::factory()->lowScore()->create([
                        'competition_id' => $competition->id,
                        'athlete_id' => $athlete->id,
                        'rank' => $rank
                    ]);
                } else {
                    CompetitionResult::factory()->create([
                        'competition_id' => $competition->id,
                        'athlete_id' => $athlete->id,
                        'rank' => $rank
                    ]);
                }
            }

            $this->command->info("Dodano wyniki dla zawodów: {$competition->name}");
        }

        // Create trainings
        $this->command->info('Tworzenie treningów...');
        foreach ($clubs as $club) {
            // Get trainers from this club
            $clubTrainers = $club->users()->wherePivot('role', 'trainer')->get();

            if ($clubTrainers->count() > 0) {
                // Create past trainings (15-25 per club)
                for ($i = 0; $i < rand(15, 25); $i++) {
                    $training = Training::factory()->past()->create([
                        'club_id' => $club->id,
                        'trainer_id' => $clubTrainers->random()->id
                    ]);

                    // Add 5-15 athletes to this training
                    $athletes = $club->athletes()->inRandomOrder()->limit(rand(5, 15))->get();
                    foreach ($athletes as $athlete) {
                        TrainingAthlete::factory()->create([
                            'training_id' => $training->id,
                            'athlete_id' => $athlete->id
                        ]);
                    }
                }

                // Create upcoming trainings (5-10 per club)
                for ($i = 0; $i < rand(5, 10); $i++) {
                    $training = Training::factory()->upcoming()->create([
                        'club_id' => $club->id,
                        'trainer_id' => $clubTrainers->random()->id
                    ]);

                    // Add 5-15 athletes to this training
                    $athletes = $club->athletes()->inRandomOrder()->limit(rand(5, 15))->get();
                    foreach ($athletes as $athlete) {
                        TrainingAthlete::factory()->create([
                            'training_id' => $training->id,
                            'athlete_id' => $athlete->id
                        ]);
                    }
                }

                // Create some technical trainings
                for ($i = 0; $i < rand(3, 5); $i++) {
                    $training = Training::factory()->technical()->create([
                        'club_id' => $club->id,
                        'trainer_id' => $clubTrainers->random()->id
                    ]);

                    // Add 3-8 athletes to technical training (smaller groups)
                    $athletes = $club->athletes()->inRandomOrder()->limit(rand(3, 8))->get();
                    foreach ($athletes as $athlete) {
                        TrainingAthlete::factory()->create([
                            'training_id' => $training->id,
                            'athlete_id' => $athlete->id
                        ]);
                    }
                }
            }

            $this->command->info("Dodano treningi dla klubu: {$club->name}");
        }

        // Create payments
        $this->command->info('Tworzenie płatności...');
        foreach ($clubs as $club) {
            $athletes = $club->athletes;

            foreach ($athletes as $athlete) {
                // Monthly fees (6-12 per athlete for past months)
                $monthlyFeeCount = rand(6, 12);
                for ($i = 0; $i < $monthlyFeeCount; $i++) {
                    Payment::factory()->monthlyFee()->create([
                        'club_id' => $club->id,
                        'athlete_id' => $athlete->id,
                        'payment_date' => now()->subMonths($i),
                    ]);
                }

                // Annual membership fee
                Payment::factory()->annualFee()->create([
                    'club_id' => $club->id,
                    'athlete_id' => $athlete->id,
                ]);

                // Competition fees (0-5 per athlete)
                $competitionFeeCount = rand(0, 5);
                for ($i = 0; $i < $competitionFeeCount; $i++) {
                    Payment::factory()->competitionFee()->create([
                        'club_id' => $club->id,
                        'athlete_id' => $athlete->id,
                    ]);
                }

                // Equipment fees (0-2 per athlete)
                $equipmentFeeCount = rand(0, 2);
                for ($i = 0; $i < $equipmentFeeCount; $i++) {
                    Payment::factory()->equipmentFee()->create([
                        'club_id' => $club->id,
                        'athlete_id' => $athlete->id,
                    ]);
                }

                // Overdue payments (20% chance per athlete)
                if (rand(1, 100) <= 20) {
                    Payment::factory()->overdue()->create([
                        'club_id' => $club->id,
                        'athlete_id' => $athlete->id,
                    ]);
                }
            }

            $this->command->info("Dodano płatności dla zawodników klubu: {$club->name}");
        }

        $this->command->info('Generowanie danych testowych zakończone pomyślnie!');
    }

    /**
     * Truncate all tables to ensure clean state - kompatybilne z SQLite
     */
    private function truncateTables(): void
    {
        // Wykorzystanie Schema::disableForeignKeyConstraints() zamiast SET FOREIGN_KEY_CHECKS=0
        Schema::disableForeignKeyConstraints();

        DB::table('training_athletes')->truncate();
        DB::table('competition_results')->truncate();
        DB::table('payments')->truncate();
        DB::table('trainings')->truncate();
        DB::table('competitions')->truncate();
        DB::table('athletes')->truncate();
        DB::table('club_user')->truncate();
        DB::table('clubs')->truncate();
        DB::table('users')->truncate();

        // Włączenie kluczy obcych
        Schema::enableForeignKeyConstraints();
    }
}
