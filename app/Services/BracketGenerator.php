<?php

namespace App\Services;

use App\Models\PoolMatch;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * BracketGenerator Service - Generates single-elimination tournament brackets.
 * 
 * This service handles:
 * - Bracket structure generation for 8, 16, or 32 player tournaments
 * - Player seeding and match assignments
 * - Match result processing and winner advancement
 */
class BracketGenerator
{
    /**
     * Generate a complete tournament bracket.
     *
     * @param Tournament $tournament
     * @return void
     * @throws \InvalidArgumentException
     */
    public function generate(Tournament $tournament): void
    {
        $players = $tournament->players()->get();
        $playerCount = $players->count();

        // Validate player count matches tournament requirements
        if ($playerCount !== $tournament->max_players) {
            throw new \InvalidArgumentException(
                "Tournament requires {$tournament->max_players} players, but {$playerCount} are registered."
            );
        }

        // Validate player count is a power of 2
        if (!$this->isPowerOfTwo($playerCount)) {
            throw new \InvalidArgumentException(
                "Player count must be a power of 2 (8, 16, or 32). Got: {$playerCount}"
            );
        }

        DB::transaction(function () use ($tournament, $players) {
            // Clear any existing matches for fresh generation
            $tournament->matches()->delete();

            // Calculate total rounds needed (log base 2)
            $totalRounds = (int) log($players->count(), 2);
            $tournament->update(['total_rounds' => $totalRounds]);

            // Seed players based on registration order (can be enhanced with ranking)
            $seededPlayers = $this->seedPlayers($players);

            // Generate all matches for all rounds
            $allMatches = $this->generateBracketStructure($tournament, $totalRounds);

            // Assign players to first round matches
            $this->assignPlayersToFirstRound($tournament, $seededPlayers, $allMatches);

            // Update tournament status to ongoing
            $tournament->update(['status' => Tournament::STATUS_ONGOING]);
        });
    }

    /**
     * Process a match result and advance the winner.
     *
     * @param PoolMatch $match
     * @param int $player1Score
     * @param int $player2Score
     * @return void
     * @throws \InvalidArgumentException
     */
    public function processMatchResult(PoolMatch $match, int $player1Score, int $player2Score): void
    {
        if (!$match->hasBothPlayers()) {
            throw new \InvalidArgumentException('Cannot process a match without both players set.');
        }

        if ($player1Score === $player2Score) {
            throw new \InvalidArgumentException('Match cannot end in a tie. There must be a winner.');
        }

        DB::transaction(function () use ($match, $player1Score, $player2Score) {
            // Determine winner
            $winnerId = $player1Score > $player2Score ? $match->player1_id : $match->player2_id;
            $loserId = $winnerId === $match->player1_id ? $match->player2_id : $match->player1_id;

            // Update match with result
            $match->update([
                'player1_score' => $player1Score,
                'player2_score' => $player2Score,
                'winner_id' => $winnerId,
                'status' => PoolMatch::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // Update player statistics
            $winner = Player::find($winnerId);
            $loser = Player::find($loserId);

            if ($winner) {
                $winner->recordWin();
            }
            if ($loser) {
                $loser->recordLoss();
            }

            // Advance winner to next match if not the final
            $this->advanceWinner($match);

            // Check if tournament is complete
            $this->checkTournamentCompletion($match->tournament);
        });
    }

    /**
     * Seed players for the tournament bracket.
     * Uses standard single-elimination seeding (1 vs max, 2 vs max-1, etc.)
     *
     * @param Collection<int, Player> $players
     * @return Collection<int, Player>
     */
    protected function seedPlayers(Collection $players): Collection
    {
        // For now, use registration order as seeding
        // This can be enhanced to use player rankings
        return $players->values();
    }

    /**
     * Generate the complete bracket structure (all matches for all rounds).
     *
     * @param Tournament $tournament
     * @param int $totalRounds
     * @return array<int, array<int, PoolMatch>>
     */
    protected function generateBracketStructure(Tournament $tournament, int $totalRounds): array
    {
        $allMatches = [];
        $matchesPerRound = $tournament->max_players / 2;

        // Create matches from first round to final
        for ($round = 1; $round <= $totalRounds; $round++) {
            $roundMatches = [];
            
            for ($matchNum = 1; $matchNum <= $matchesPerRound; $matchNum++) {
                $match = PoolMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'match_number' => $matchNum,
                    'status' => PoolMatch::STATUS_PENDING,
                ]);
                $roundMatches[$matchNum] = $match;
            }
            
            $allMatches[$round] = $roundMatches;
            $matchesPerRound = $matchesPerRound / 2;
        }

        // Link matches to their next match in the bracket
        $this->linkMatchesToNextRound($allMatches);

        return $allMatches;
    }

    /**
     * Link each match to its subsequent match in the next round.
     *
     * @param array<int, array<int, PoolMatch>> $allMatches
     * @return void
     */
    protected function linkMatchesToNextRound(array $allMatches): void
    {
        $totalRounds = count($allMatches);

        for ($round = 1; $round < $totalRounds; $round++) {
            $currentRoundMatches = $allMatches[$round];
            $nextRoundMatches = $allMatches[$round + 1];

            foreach ($currentRoundMatches as $matchNum => $match) {
                // Two consecutive matches in current round feed into one match in next round
                // Match 1,2 -> Next Match 1
                // Match 3,4 -> Next Match 2
                $nextMatchNum = (int) ceil($matchNum / 2);
                $nextMatch = $nextRoundMatches[$nextMatchNum];

                $match->update(['next_match_id' => $nextMatch->id]);
            }
        }
    }

    /**
     * Assign seeded players to first round matches.
     *
     * @param Tournament $tournament
     * @param Collection<int, Player> $seededPlayers
     * @param array<int, array<int, PoolMatch>> $allMatches
     * @return void
     */
    protected function assignPlayersToFirstRound(
        Tournament $tournament, 
        Collection $seededPlayers, 
        array $allMatches
    ): void {
        $firstRoundMatches = $allMatches[1];
        $playerCount = $seededPlayers->count();
        
        // Standard single-elimination bracket seeding
        // Creates matchups like: 1v8, 4v5, 2v7, 3v6 for 8 players
        $bracketOrder = $this->getBracketSeedOrder($playerCount);
        
        $matchIndex = 0;
        foreach ($firstRoundMatches as $match) {
            $player1Index = $bracketOrder[$matchIndex * 2];
            $player2Index = $bracketOrder[$matchIndex * 2 + 1];

            $match->update([
                'player1_id' => $seededPlayers[$player1Index]->id,
                'player2_id' => $seededPlayers[$player2Index]->id,
            ]);
            
            $matchIndex++;
        }
    }

    /**
     * Get the bracket seeding order for standard single-elimination format.
     * Uses alternating top/bottom seed pairing (1 vs 8, 2 vs 7, etc.)
     *
     * @param int $playerCount
     * @return array<int>
     */
    protected function getBracketSeedOrder(int $playerCount): array
    {
        // Alternate between top and bottom seeds for fair matchups
        // Example for 8 players: [0,7, 1,6, 2,5, 3,4] = 1v8, 2v7, 3v6, 4v5
        $result = [];
        for ($i = 0; $i < $playerCount / 2; $i++) {
            $result[] = $i;
            $result[] = $playerCount - 1 - $i;
        }
        
        return $result;
    }

    /**
     * Advance the winner to the next match in the bracket.
     *
     * @param PoolMatch $match
     * @return void
     */
    protected function advanceWinner(PoolMatch $match): void
    {
        if (!$match->next_match_id || !$match->winner_id) {
            return;
        }

        $nextMatch = PoolMatch::find($match->next_match_id);
        if (!$nextMatch) {
            return;
        }

        // Determine if winner goes to player1 or player2 slot
        // Odd match numbers go to player1, even to player2 of next match
        if ($match->match_number % 2 === 1) {
            $nextMatch->update(['player1_id' => $match->winner_id]);
        } else {
            $nextMatch->update(['player2_id' => $match->winner_id]);
        }
    }

    /**
     * Check if the tournament is complete and update status.
     *
     * @param Tournament $tournament
     * @return void
     */
    protected function checkTournamentCompletion(Tournament $tournament): void
    {
        // Get the final match
        $finalMatch = $tournament->matches()
            ->where('round', $tournament->total_rounds)
            ->first();

        if ($finalMatch && $finalMatch->isCompleted()) {
            $tournament->update([
                'status' => Tournament::STATUS_FINISHED,
                'end_date' => now(),
            ]);
        }
    }

    /**
     * Check if a number is a power of 2.
     *
     * @param int $n
     * @return bool
     */
    protected function isPowerOfTwo(int $n): bool
    {
        return $n > 0 && ($n & ($n - 1)) === 0;
    }
}
