<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\User;
use App\Services\BracketGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder - Seeds the database with demo data for testing.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@theatropool.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Regular User
        User::create([
            'name' => 'John Player',
            'email' => 'player@theatropool.com',
            'password' => Hash::make('password'),
            'role' => 'player',
        ]);

        // Create Demo Players
        $players = [
            ['name' => 'Mike "The Shark" Thompson', 'nickname' => 'The Shark', 'email' => 'mike@example.com'],
            ['name' => 'David Chen', 'nickname' => 'Dragon', 'email' => 'david@example.com'],
            ['name' => 'Sarah Williams', 'nickname' => 'Queen', 'email' => 'sarah@example.com'],
            ['name' => 'James Rodriguez', 'nickname' => 'El Maestro', 'email' => 'james@example.com'],
            ['name' => 'Emily Brown', 'nickname' => 'Eagle Eye', 'email' => 'emily@example.com'],
            ['name' => 'Robert Taylor', 'nickname' => 'Bobby T', 'email' => 'robert@example.com'],
            ['name' => 'Lisa Anderson', 'nickname' => null, 'email' => 'lisa@example.com'],
            ['name' => 'Michael Johnson', 'nickname' => 'MJ', 'email' => 'mj@example.com'],
            ['name' => 'Jennifer Davis', 'nickname' => 'Jen', 'email' => 'jen@example.com'],
            ['name' => 'William Martinez', 'nickname' => 'Will Power', 'email' => 'will@example.com'],
            ['name' => 'Amanda Garcia', 'nickname' => null, 'email' => 'amanda@example.com'],
            ['name' => 'Christopher Lee', 'nickname' => 'Chris', 'email' => 'chris@example.com'],
            ['name' => 'Jessica Wilson', 'nickname' => 'Jess', 'email' => 'jessica@example.com'],
            ['name' => 'Daniel Moore', 'nickname' => 'Danny', 'email' => 'daniel@example.com'],
            ['name' => 'Ashley Thomas', 'nickname' => null, 'email' => 'ashley@example.com'],
            ['name' => 'Matthew Jackson', 'nickname' => 'Matt', 'email' => 'matt@example.com'],
        ];

        foreach ($players as $playerData) {
            Player::create($playerData);
        }

        // Create Upcoming Tournament (8 players, not full)
        $upcomingTournament = Tournament::create([
            'name' => 'Winter Pool Championship 2024',
            'description' => 'Annual winter tournament featuring the best pool players in the region.',
            'start_date' => now()->addWeeks(2),
            'location' => 'Theatro Pool Hall - Main Room',
            'max_players' => 8,
            'status' => 'upcoming',
        ]);

        // Register 5 players to the upcoming tournament
        $playersForUpcoming = Player::take(5)->get();
        foreach ($playersForUpcoming as $index => $player) {
            $upcomingTournament->players()->attach($player->id, ['seed' => $index + 1]);
        }

        // Create Another Upcoming Tournament (16 players, full - ready to start)
        $fullTournament = Tournament::create([
            'name' => 'Spring Showdown 2024',
            'description' => 'Get ready for an exciting spring tournament!',
            'start_date' => now()->addMonth(),
            'location' => 'Theatro Pool Hall - Tournament Arena',
            'max_players' => 16,
            'status' => 'upcoming',
        ]);

        // Register all 16 players
        $allPlayers = Player::all();
        foreach ($allPlayers as $index => $player) {
            $fullTournament->players()->attach($player->id, ['seed' => $index + 1]);
        }

        // Create Ongoing Tournament (8 players) with some matches played
        $ongoingTournament = Tournament::create([
            'name' => 'February Cup 2024',
            'description' => 'Monthly tournament series',
            'start_date' => now()->subDays(2),
            'location' => 'Theatro Pool Hall - VIP Room',
            'max_players' => 8,
            'status' => 'upcoming',
        ]);

        // Register 8 players
        $playersForOngoing = Player::take(8)->get();
        foreach ($playersForOngoing as $index => $player) {
            $ongoingTournament->players()->attach($player->id, ['seed' => $index + 1]);
        }

        // Generate bracket and play some matches
        $bracketGenerator = new BracketGenerator();
        $bracketGenerator->generate($ongoingTournament);

        // Play first round matches (get fresh matches)
        $ongoingTournament->refresh();
        $firstRoundMatches = $ongoingTournament->matches()->where('round', 1)->get();
        
        foreach ($firstRoundMatches as $match) {
            // Generate unique random scores (avoid ties)
            list($score1, $score2) = $this->generateRandomScores();
            $bracketGenerator->processMatchResult($match, $score1, $score2);
        }

        // Create Finished Tournament (8 players) - fully completed
        $finishedTournament = Tournament::create([
            'name' => 'New Year Classic 2024',
            'description' => 'The first tournament of the year!',
            'start_date' => now()->subMonth(),
            'location' => 'Theatro Pool Hall',
            'max_players' => 8,
            'status' => 'upcoming',
        ]);

        // Register 8 different players (rotate the list)
        $playersForFinished = Player::skip(4)->take(8)->get();
        if ($playersForFinished->count() < 8) {
            $playersForFinished = Player::take(8)->get();
        }
        
        foreach ($playersForFinished as $index => $player) {
            $finishedTournament->players()->attach($player->id, ['seed' => $index + 1]);
        }

        // Generate and complete all matches
        $bracketGenerator->generate($finishedTournament);
        $finishedTournament->refresh();
        
        // Play all rounds until tournament is complete
        for ($round = 1; $round <= $finishedTournament->total_rounds; $round++) {
            $roundMatches = $finishedTournament->matches()->where('round', $round)->get();
            foreach ($roundMatches as $match) {
                $match->refresh();
                if ($match->hasBothPlayers() && !$match->isCompleted()) {
                    list($score1, $score2) = $this->generateRandomScores();
                    $bracketGenerator->processMatchResult($match, $score1, $score2);
                }
            }
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  Admin: admin@theatropool.com / password');
        $this->command->info('  Player: player@theatropool.com / password');
    }

    /**
     * Generate random scores ensuring they are not equal.
     * Uses a safety counter to prevent infinite loops.
     *
     * @return array{0: int, 1: int}
     */
    private function generateRandomScores(): array
    {
        $score1 = rand(3, 7);
        $score2 = rand(3, 7);
        $attempts = 0;
        $maxAttempts = 10;
        
        while ($score1 === $score2 && $attempts < $maxAttempts) {
            $score2 = rand(3, 7);
            $attempts++;
        }
        
        // Fallback: ensure scores are different
        if ($score1 === $score2) {
            $score2 = $score1 > 3 ? $score1 - 1 : $score1 + 1;
        }
        
        return [$score1, $score2];
    }
}
