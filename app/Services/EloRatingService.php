<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PoolMatch;
use Illuminate\Support\Facades\DB;

/**
 * EloRatingService - Calculates and manages ELO ratings for players.
 *
 * The ELO rating system provides a fair ranking of player skill levels.
 * - New players start at 1000 points
 * - K-factor varies based on player experience
 * - Win against higher-rated = more points gained
 * - Win against lower-rated = fewer points gained
 */
class EloRatingService
{
    /**
     * Base K-factor for rating calculations.
     */
    protected const BASE_K_FACTOR = 32;

    /**
     * Minimum K-factor (for experienced players).
     */
    protected const MIN_K_FACTOR = 16;

    /**
     * Default starting rating.
     */
    public const DEFAULT_RATING = 1000;

    /**
     * Calculate new ELO ratings after a match.
     *
     * @param Player $winner
     * @param Player $loser
     * @param int $winnerScore
     * @param int $loserScore
     * @return array ['winner_new_rating' => int, 'loser_new_rating' => int, 'winner_change' => int, 'loser_change' => int]
     */
    public function calculateNewRatings(
        Player $winner,
        Player $loser,
        int $winnerScore,
        int $loserScore
    ): array {
        $winnerRating = $winner->ranking_points ?? self::DEFAULT_RATING;
        $loserRating = $loser->ranking_points ?? self::DEFAULT_RATING;

        // Calculate expected scores
        $winnerExpected = $this->expectedScore($winnerRating, $loserRating);
        $loserExpected = $this->expectedScore($loserRating, $winnerRating);

        // Calculate margin of victory multiplier (closer games = less points)
        $marginMultiplier = $this->marginMultiplier($winnerScore, $loserScore);

        // Get K-factors based on player experience
        $winnerK = $this->getKFactor($winner);
        $loserK = $this->getKFactor($loser);

        // Calculate rating changes
        // Winner always gets points (actual score = 1)
        $winnerChange = (int) round($winnerK * $marginMultiplier * (1 - $winnerExpected));
        // Loser always loses points (actual score = 0)
        $loserChange = (int) round($loserK * $marginMultiplier * (0 - $loserExpected));

        // Ensure minimum change of 1 point
        $winnerChange = max(1, $winnerChange);
        $loserChange = min(-1, $loserChange);

        return [
            'winner_new_rating' => $winnerRating + $winnerChange,
            'loser_new_rating' => max(100, $loserRating + $loserChange), // Minimum 100 rating
            'winner_change' => $winnerChange,
            'loser_change' => $loserChange,
        ];
    }

    /**
     * Update player ratings after a match result.
     *
     * @param PoolMatch $match
     * @return array Rating changes
     */
    public function updateRatingsForMatch(PoolMatch $match): array
    {
        if (!$match->isCompleted() || !$match->winner_id) {
            throw new \InvalidArgumentException('Match must be completed with a winner.');
        }

        $winner = $match->winner;
        $loser = $match->player1_id === $match->winner_id ? $match->player2 : $match->player1;

        $ratings = $this->calculateNewRatings(
            $winner,
            $loser,
            $match->player1_id === $match->winner_id ? $match->player1_score : $match->player2_score,
            $match->player1_id === $match->winner_id ? $match->player2_score : $match->player1_score
        );

        DB::transaction(function () use ($winner, $loser, $ratings) {
            $winner->update(['ranking_points' => $ratings['winner_new_rating']]);
            $loser->update(['ranking_points' => $ratings['loser_new_rating']]);
        });

        return $ratings;
    }

    /**
     * Reverse rating changes (for admin overrides).
     *
     * @param PoolMatch $match
     * @param int $previousWinnerId
     * @return void
     */
    public function reverseRatings(PoolMatch $match, int $previousWinnerId): void
    {
        $previousWinner = Player::find($previousWinnerId);
        $previousLoser = $match->player1_id === $previousWinnerId ? $match->player2 : $match->player1;

        if (!$previousWinner || !$previousLoser) {
            return;
        }

        // Estimate reversal (simplified - ideally store rating history)
        $reverseRatings = $this->calculateNewRatings(
            $previousWinner,
            $previousLoser,
            $match->player1_id === $previousWinnerId ? ($match->player1_score ?? 5) : ($match->player2_score ?? 5),
            $match->player1_id === $previousWinnerId ? ($match->player2_score ?? 3) : ($match->player1_score ?? 3)
        );

        DB::transaction(function () use ($previousWinner, $previousLoser, $reverseRatings) {
            // Reverse: winner loses points, loser gains points
            $previousWinner->update([
                'ranking_points' => max(100, ($previousWinner->ranking_points ?? self::DEFAULT_RATING) - $reverseRatings['winner_change'])
            ]);
            $previousLoser->update([
                'ranking_points' => ($previousLoser->ranking_points ?? self::DEFAULT_RATING) - $reverseRatings['loser_change']
            ]);
        });
    }

    /**
     * Get the global leaderboard.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeaderboard(int $limit = 50)
    {
        return Player::where('total_matches', '>', 0)
            ->orderByDesc('ranking_points')
            ->take($limit)
            ->get();
    }

    /**
     * Get player's rank position.
     *
     * @param Player $player
     * @return int
     */
    public function getPlayerRank(Player $player): int
    {
        return Player::where('ranking_points', '>', $player->ranking_points ?? self::DEFAULT_RATING)
            ->where('total_matches', '>', 0)
            ->count() + 1;
    }

    /**
     * Get rating tier/title for a player.
     *
     * @param int $rating
     * @return array ['tier' => string, 'title' => string, 'color' => string]
     */
    public function getRatingTier(int $rating): array
    {
        return match (true) {
            $rating >= 2000 => ['tier' => 'grandmaster', 'title' => 'Grandmaster', 'color' => 'text-purple-600'],
            $rating >= 1800 => ['tier' => 'master', 'title' => 'Master', 'color' => 'text-red-600'],
            $rating >= 1600 => ['tier' => 'expert', 'title' => 'Expert', 'color' => 'text-orange-600'],
            $rating >= 1400 => ['tier' => 'advanced', 'title' => 'Advanced', 'color' => 'text-yellow-600'],
            $rating >= 1200 => ['tier' => 'intermediate', 'title' => 'Intermediate', 'color' => 'text-green-600'],
            $rating >= 1000 => ['tier' => 'amateur', 'title' => 'Amateur', 'color' => 'text-blue-600'],
            default => ['tier' => 'beginner', 'title' => 'Beginner', 'color' => 'text-gray-600'],
        };
    }

    /**
     * Calculate expected score (probability of winning).
     *
     * @param int $playerRating
     * @param int $opponentRating
     * @return float Between 0 and 1
     */
    protected function expectedScore(int $playerRating, int $opponentRating): float
    {
        return 1 / (1 + pow(10, ($opponentRating - $playerRating) / 400));
    }

    /**
     * Get K-factor based on player experience.
     *
     * @param Player $player
     * @return int
     */
    protected function getKFactor(Player $player): int
    {
        $totalMatches = $player->total_matches ?? 0;
        $rating = $player->ranking_points ?? self::DEFAULT_RATING;

        // New players (< 10 matches) have higher K-factor
        if ($totalMatches < 10) {
            return 40;
        }

        // High-rated players have lower K-factor for stability
        if ($rating >= 1800) {
            return self::MIN_K_FACTOR;
        }

        // Intermediate players
        if ($totalMatches < 30) {
            return 24;
        }

        return self::BASE_K_FACTOR - min(16, (int) ($totalMatches / 20));
    }

    /**
     * Calculate margin of victory multiplier.
     * Bigger blowouts are worth slightly more.
     *
     * @param int $winnerScore
     * @param int $loserScore
     * @return float Between 1.0 and 1.5
     */
    protected function marginMultiplier(int $winnerScore, int $loserScore): float
    {
        $margin = $winnerScore - $loserScore;
        $total = $winnerScore + $loserScore;

        if ($total === 0) {
            return 1.0;
        }

        // Cap the multiplier between 1.0 and 1.5
        return min(1.5, 1.0 + ($margin / $total) * 0.5);
    }

    /**
     * Recalculate all player ratings from scratch.
     * Use this for fixing rating issues or after algorithm changes.
     *
     * @return int Number of matches processed
     */
    public function recalculateAllRatings(): int
    {
        // Reset all ratings to default
        Player::query()->update(['ranking_points' => self::DEFAULT_RATING]);

        // Process all completed matches in chronological order
        $matches = PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)
            ->whereNotNull('winner_id')
            ->orderBy('completed_at')
            ->get();

        foreach ($matches as $match) {
            try {
                $this->updateRatingsForMatch($match);
            } catch (\Exception $e) {
                // Skip problematic matches
                continue;
            }
        }

        return $matches->count();
    }
}
