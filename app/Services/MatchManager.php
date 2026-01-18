<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PoolMatch;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

/**
 * MatchManager - Handles all match-related operations.
 *
 * Responsibilities:
 * - Recording match results
 * - Advancing winners to next matches
 * - Admin result overrides
 * - Auto-completing tournaments
 * - Match scheduling
 */
class MatchManager
{
    protected BracketGenerator $bracketGenerator;

    public function __construct(BracketGenerator $bracketGenerator)
    {
        $this->bracketGenerator = $bracketGenerator;
    }

    /**
     * Record a match result.
     *
     * @param PoolMatch $match
     * @param int $player1Score
     * @param int $player2Score
     * @return void
     */
    public function recordResult(PoolMatch $match, int $player1Score, int $player2Score): void
    {
        $this->bracketGenerator->processMatchResult($match, $player1Score, $player2Score, false);
    }

    /**
     * Admin override: Manually set match result.
     *
     * @param PoolMatch $match
     * @param int $winnerId
     * @param int $player1Score
     * @param int $player2Score
     * @return void
     */
    public function overrideResult(PoolMatch $match, int $winnerId, int $player1Score, int $player2Score): void
    {
        if (!in_array($winnerId, [$match->player1_id, $match->player2_id])) {
            throw new \InvalidArgumentException('Winner must be one of the match players.');
        }

        DB::transaction(function () use ($match, $winnerId, $player1Score, $player2Score) {
            // If match was already completed, undo statistics
            if ($match->isCompleted() && $match->winner_id) {
                $this->reverseMatchStats($match);
            }

            // Record new result with override flag
            $match->update([
                'player1_score' => $player1Score,
                'player2_score' => $player2Score,
                'winner_id' => $winnerId,
                'status' => PoolMatch::STATUS_COMPLETED,
                'completed_at' => now(),
                'is_admin_override' => true,
            ]);

            // Update player statistics
            $winner = Player::find($winnerId);
            $loser = Player::find($match->player1_id === $winnerId ? $match->player2_id : $match->player1_id);

            if ($winner) {
                $winner->recordWin();
            }
            if ($loser) {
                $loser->recordLoss();
            }

            // Advance winner
            $this->advanceWinner($match);
            $this->checkTournamentCompletion($match->tournament);
        });
    }

    /**
     * Schedule a match for a specific time and table.
     *
     * @param PoolMatch $match
     * @param \Carbon\Carbon $scheduledTime
     * @param int|null $tableNumber
     * @param int|null $refereeId
     * @return void
     */
    public function scheduleMatch(
        PoolMatch $match,
        \Carbon\Carbon $scheduledTime,
        ?int $tableNumber = null,
        ?int $refereeId = null
    ): void {
        $match->schedule($scheduledTime, $tableNumber, $refereeId);
    }

    /**
     * Start a match (change status to in_progress).
     *
     * @param PoolMatch $match
     * @return void
     */
    public function startMatch(PoolMatch $match): void
    {
        if (!$match->hasBothPlayers()) {
            throw new \InvalidArgumentException('Cannot start match without both players assigned.');
        }

        $match->start();
    }

    /**
     * Advance winner to next match.
     *
     * @param PoolMatch $match
     * @return PoolMatch|null
     */
    public function advanceWinner(PoolMatch $match): ?PoolMatch
    {
        if (!$match->winner_id || !$match->nextMatch) {
            return null;
        }

        $nextMatch = $match->nextMatch;

        // Determine which slot the winner fills
        if ($nextMatch->player1_id === null) {
            $nextMatch->update(['player1_id' => $match->winner_id]);
        } elseif ($nextMatch->player2_id === null) {
            $nextMatch->update(['player2_id' => $match->winner_id]);
        }

        // Check if both players are set
        if ($nextMatch->hasBothPlayers()) {
            $nextMatch->update(['status' => PoolMatch::STATUS_PENDING]);
        }

        return $nextMatch;
    }

    /**
     * Get all pending matches for a tournament.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingMatches(Tournament $tournament)
    {
        return $tournament->matches()
            ->where('status', PoolMatch::STATUS_PENDING)
            ->whereNotNull('player1_id')
            ->whereNotNull('player2_id')
            ->orderBy('round')
            ->orderBy('match_number')
            ->get();
    }

    /**
     * Get all scheduled matches for a tournament.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getScheduledMatches(Tournament $tournament)
    {
        return $tournament->matches()
            ->where('status', PoolMatch::STATUS_SCHEDULED)
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get all completed matches for a tournament.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompletedMatches(Tournament $tournament)
    {
        return $tournament->matches()
            ->where('status', PoolMatch::STATUS_COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Get match statistics for a player in a tournament.
     *
     * @param Tournament $tournament
     * @param Player $player
     * @return array
     */
    public function getPlayerStats(Tournament $tournament, Player $player): array
    {
        $matches = $tournament->matches()
            ->where(function ($query) use ($player) {
                $query->where('player1_id', $player->id)
                    ->orWhere('player2_id', $player->id);
            })
            ->get();

        $wins = $matches->where('winner_id', $player->id)->count();
        $losses = $matches->where('winner_id', '!=', $player->id)
            ->where('status', PoolMatch::STATUS_COMPLETED)
            ->count();
        $total = $matches->where('status', PoolMatch::STATUS_COMPLETED)->count();

        return [
            'wins' => $wins,
            'losses' => $losses,
            'total_matches' => $total,
            'win_rate' => $total > 0 ? round(($wins / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get tournament standings (leaderboard).
     *
     * @param Tournament $tournament
     * @return array
     */
    public function getTournamentStandings(Tournament $tournament): array
    {
        $standings = [];

        foreach ($tournament->players as $player) {
            $stats = $this->getPlayerStats($tournament, $player);
            $standings[] = [
                'player' => $player,
                'wins' => $stats['wins'],
                'losses' => $stats['losses'],
                'total_matches' => $stats['total_matches'],
                'win_rate' => $stats['win_rate'],
            ];
        }

        // Sort by wins (desc), then by win rate (desc)
        usort($standings, function ($a, $b) {
            if ($a['wins'] !== $b['wins']) {
                return $b['wins'] - $a['wins'];
            }
            return $b['win_rate'] <=> $a['win_rate'];
        });

        // Add rank
        foreach ($standings as $key => &$standing) {
            $standing['rank'] = $key + 1;
        }

        return $standings;
    }

    /**
     * Get bracket structure for visualization.
     *
     * @param Tournament $tournament
     * @return array
     */
    public function getBracketData(Tournament $tournament): array
    {
        $matches = $tournament->matches()
            ->orderBy('round')
            ->orderBy('match_number')
            ->get();

        $brackets = [];
        foreach ($matches->groupBy('round') as $round => $roundMatches) {
            $brackets[$round] = $roundMatches->map(function ($match) {
                return [
                    'id' => $match->id,
                    'round' => $match->round,
                    'match_number' => $match->match_number,
                    'player1' => $match->player1 ? [
                        'id' => $match->player1->id,
                        'name' => $match->player1->display_name,
                        'score' => $match->player1_score,
                    ] : null,
                    'player2' => $match->player2 ? [
                        'id' => $match->player2->id,
                        'name' => $match->player2->display_name,
                        'score' => $match->player2_score,
                    ] : null,
                    'winner_id' => $match->winner_id,
                    'status' => $match->status,
                    'scheduled_at' => $match->scheduled_at,
                    'table_number' => $match->table_number,
                    'referee' => $match->referee ? $match->referee->display_name : null,
                ];
            })->toArray();
        }

        return $brackets;
    }

    /**
     * Check if tournament is complete and update status.
     *
     * @param Tournament $tournament
     * @return bool
     */
    protected function checkTournamentCompletion(Tournament $tournament): bool
    {
        // Get the final match
        $finalMatch = $tournament->matches()
            ->where('round', $tournament->total_rounds)
            ->first();

        if ($finalMatch && $finalMatch->isCompleted()) {
            $tournament->update([
                'status' => Tournament::STATUS_FINISHED,
                'champion_id' => $finalMatch->winner_id,
                'end_date' => now(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Reverse match statistics (for admin overrides).
     *
     * @param PoolMatch $match
     * @return void
     */
    protected function reverseMatchStats(PoolMatch $match): void
    {
        if (!$match->winner_id) {
            return;
        }

        $winner = Player::find($match->winner_id);
        $loser = Player::find($match->player1_id === $match->winner_id ? $match->player2_id : $match->player1_id);

        if ($winner) {
            $winner->decrement('wins');
            $winner->decrement('total_matches');
        }
        if ($loser) {
            $loser->decrement('losses');
            $loser->decrement('total_matches');
        }
    }
}
