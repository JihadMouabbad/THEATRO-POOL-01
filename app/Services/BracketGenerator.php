<?php

namespace App\Services;

use App\Models\PoolMatch;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BracketGenerator Service - Generates tournament brackets.
 *
 * This service handles:
 * - Single elimination, double elimination, and round-robin brackets
 * - Bracket structure generation for any player count
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

        if ($playerCount === 0) {
            throw new \InvalidArgumentException('Tournament has no registered players.');
        }

        DB::transaction(function () use ($tournament, $players) {
            // Clear any existing matches for fresh generation
            $tournament->matches()->delete();

            // Seed players based on registration order (can be enhanced with ranking)
            $seededPlayers = $this->seedPlayers($players);

            // Generate bracket based on tournament type
            match ($tournament->bracket_type) {
                Tournament::BRACKET_SINGLE_ELIMINATION => $this->generateSingleElimination($tournament, $seededPlayers),
                Tournament::BRACKET_DOUBLE_ELIMINATION => $this->generateDoubleElimination($tournament, $seededPlayers),
                Tournament::BRACKET_ROUND_ROBIN => $this->generateRoundRobin($tournament, $seededPlayers),
                default => throw new \InvalidArgumentException("Unknown bracket type: {$tournament->bracket_type}"),
            };

            // Update tournament status to ongoing
            $tournament->update(['status' => Tournament::STATUS_ONGOING]);
        });
    }

    /**
     * Generate single elimination bracket.
     *
     * @param Tournament $tournament
     * @param Collection $seededPlayers
     * @return void
     */
    protected function generateSingleElimination(Tournament $tournament, Collection $seededPlayers): void
    {
        // Validate player count is a power of 2
        $playerCount = $seededPlayers->count();
        if (!$this->isPowerOfTwo($playerCount)) {
            throw new \InvalidArgumentException(
                "Single elimination requires a power of 2 players (8, 16, 32, etc.). Got: {$playerCount}"
            );
        }

        // Calculate total rounds needed (log base 2)
        $totalRounds = (int) log($playerCount, 2);
        $tournament->update(['total_rounds' => $totalRounds]);

        // Generate all matches for all rounds
        $allMatches = $this->generateBracketStructure($tournament, $totalRounds);

        // Assign players to first round matches
        $this->assignPlayersToFirstRound($tournament, $seededPlayers, $allMatches);
    }

    /**
     * Generate double elimination bracket.
     *
     * @param Tournament $tournament
     * @param Collection $seededPlayers
     * @return void
     */
    protected function generateDoubleElimination(Tournament $tournament, Collection $seededPlayers): void
    {
        $playerCount = $seededPlayers->count();
        $winnersRounds = $this->isPowerOfTwo($playerCount) ? (int) log($playerCount, 2) : $this->calculateRoundsForAny($playerCount);
        $losersRounds = $winnersRounds - 1;

        // Grand final is separate round
        $tournament->update(['total_rounds' => $winnersRounds + $losersRounds + 1]);

        // Generate winners bracket (standard single elimination)
        $winnersMatches = $this->generateBracketStructure($tournament, $winnersRounds);
        $this->assignPlayersToFirstRound($tournament, $seededPlayers, $winnersMatches);

        // Losers bracket matches are created as losers are eliminated from winners bracket
        // (Handled by MatchManager)
    }

    /**
     * Generate round-robin bracket.
     *
     * @param Tournament $tournament
     * @param Collection $seededPlayers
     * @return void
     */
    protected function generateRoundRobin(Tournament $tournament, Collection $seededPlayers): void
    {
        $playerCount = $seededPlayers->count();
        $tournament->update(['total_rounds' => $playerCount - 1]);

        $playerList = $seededPlayers->values();
        $playerIds = $playerList->pluck('id')->toArray();
        $matchNumber = 1;

        // Generate all pairings using round-robin algorithm
        for ($round = 1; $round <= $playerCount - 1; $round++) {
            for ($i = 0; $i < intval($playerCount / 2); $i++) {
                $player1Id = $playerIds[$i];
                $player2Id = $playerIds[$playerCount - 1 - $i];

                if ($player1Id !== $player2Id) {
                    PoolMatch::create([
                        'tournament_id' => $tournament->id,
                        'round' => $round,
                        'match_number' => $matchNumber++,
                        'player1_id' => $player1Id,
                        'player2_id' => $player2Id,
                        'status' => PoolMatch::STATUS_PENDING,
                        'match_format' => PoolMatch::FORMAT_RACE_TO,
                        'frames_to_win' => 5,
                    ]);
                }
            }

            // Rotate players for next round (keep first player fixed)
            $lastPlayer = array_pop($playerIds);
            array_splice($playerIds, 1, 0, $lastPlayer);
            $matchNumber = 1; // Reset for next round
        }
    }

    /**
     * Process a match result and advance the winner.
     *
     * @param PoolMatch $match
     * @param int $player1Score
     * @param int $player2Score
     * @param bool $isOverride
     * @return void
     * @throws \InvalidArgumentException
     */
    public function processMatchResult(PoolMatch $match, int $player1Score, int $player2Score, bool $isOverride = false): void
    {
        if (!$match->hasBothPlayers()) {
            throw new \InvalidArgumentException('Cannot process a match without both players set.');
        }

        if (!$isOverride && $player1Score === $player2Score) {
            throw new \InvalidArgumentException('Match cannot end in a tie. There must be a winner.');
        }

        DB::transaction(function () use ($match, $player1Score, $player2Score, $isOverride) {
            // Determine winner
            $winnerId = $player1Score > $player2Score ? $match->player1_id : $match->player2_id;
            $loserId = $winnerId === $match->player1_id ? $match->player2_id : $match->player1_id;

            // Update match with result
            $match->recordResult($player1Score, $player2Score, $isOverride);

            // Update player statistics
            $winner = Player::find($winnerId);
            $loser = Player::find($loserId);

            if ($winner) {
                $winner->recordWin();
            }
            if ($loser) {
                $loser->recordLoss();
            }

            // Update ELO ratings
            try {
                $eloService = app(EloRatingService::class);
                $eloService->updateRatingsForMatch($match->fresh());
            } catch (\Exception $e) {
                // Log but don't fail - ELO is supplementary
                Log::warning('ELO update failed: ' . $e->getMessage());
            }

            // Advance winner to next match if exists
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

    /**
     * Calculate rounds needed for any player count (not just powers of 2).
     * Uses ceiling calculation to handle odd numbers.
     *
     * @param int $playerCount
     * @return int
     */
    protected function calculateRoundsForAny(int $playerCount): int
    {
        return (int) ceil(log($playerCount, 2));
    }
}
