<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PoolMatch model - represents a single match between two players in a tournament.
 * Named 'PoolMatch' to avoid conflict with PHP 8.0+'s 'match' reserved keyword.
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $round
 * @property int $match_number
 * @property int|null $player1_id
 * @property int|null $player2_id
 * @property int|null $player1_score
 * @property int|null $player2_score
 * @property int|null $winner_id
 * @property int|null $next_match_id
 * @property string $status
 * @property \Carbon\Carbon|null $scheduled_at
 * @property int|null $table_number
 * @property int|null $referee_id
 * @property string $match_format
 * @property int $frames_to_win
 * @property bool $is_admin_override
 * @property \Carbon\Carbon|null $completed_at
 */
class PoolMatch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * Match status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Match format constants.
     */
    public const FORMAT_RACE_TO = 'race_to';
    public const FORMAT_BEST_OF = 'best_of';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tournament_id',
        'round',
        'match_number',
        'player1_id',
        'player2_id',
        'player1_score',
        'player2_score',
        'winner_id',
        'next_match_id',
        'status',
        'scheduled_at',
        'table_number',
        'referee_id',
        'match_format',
        'frames_to_win',
        'is_admin_override',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'round' => 'integer',
        'match_number' => 'integer',
        'player1_score' => 'integer',
        'player2_score' => 'integer',
        'table_number' => 'integer',
        'frames_to_win' => 'integer',
        'is_admin_override' => 'boolean',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the tournament this match belongs to.
     *
     * @return BelongsTo<Tournament, PoolMatch>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get player 1.
     *
     * @return BelongsTo<Player, PoolMatch>
     */
    public function player1(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    /**
     * Get player 2.
     *
     * @return BelongsTo<Player, PoolMatch>
     */
    public function player2(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    /**
     * Get the winner of this match.
     *
     * @return BelongsTo<Player, PoolMatch>
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'winner_id');
    }

    /**
     * Get the next match the winner advances to.
     *
     * @return BelongsTo<PoolMatch, PoolMatch>
     */
    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(PoolMatch::class, 'next_match_id');
    }

    /**
     * Get the referee assigned to this match.
     *
     * @return BelongsTo<Player, PoolMatch>
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'referee_id');
    }

    /**
     * Check if the match is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the match is scheduled.
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if the match is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if both players are set.
     *
     * @return bool
     */
    public function hasBothPlayers(): bool
    {
        return $this->player1_id !== null && $this->player2_id !== null;
    }

    /**
     * Get the loser of this match.
     *
     * @return Player|null
     */
    public function getLoser(): ?Player
    {
        if (!$this->isCompleted() || !$this->winner_id) {
            return null;
        }

        if ($this->winner_id === $this->player1_id) {
            return $this->player2;
        }

        return $this->player1;
    }

    /**
     * Get a display label for the match.
     *
     * @return string
     */
    public function getDisplayLabelAttribute(): string
    {
        $p1 = $this->player1?->display_name ?? 'TBD';
        $p2 = $this->player2?->display_name ?? 'TBD';

        return "{$p1} vs {$p2}";
    }

    /**
     * Get the score display string.
     *
     * @return string
     */
    public function getScoreDisplayAttribute(): string
    {
        if (!$this->isCompleted()) {
            return '-';
        }

        return "{$this->player1_score} - {$this->player2_score}";
    }

    /**
     * Determine the match result winner by comparing scores.
     *
     * @return int|null
     */
    public function determineWinner(): ?int
    {
        if (!$this->player1_score || !$this->player2_score) {
            return null;
        }

        return $this->player1_score > $this->player2_score
            ? $this->player1_id
            : $this->player2_id;
    }

    /**
     * Check if match is ready to be completed (both scores entered).
     *
     * @return bool
     */
    public function isReadyToComplete(): bool
    {
        return $this->player1_score !== null && $this->player2_score !== null;
    }

    /**
     * Record match result and advance winner.
     *
     * @param int $player1Score
     * @param int $player2Score
     * @param bool $isOverride
     * @return void
     */
    public function recordResult(int $player1Score, int $player2Score, bool $isOverride = false): void
    {
        $this->player1_score = $player1Score;
        $this->player2_score = $player2Score;
        $this->winner_id = $this->determineWinner();
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->is_admin_override = $isOverride;
        $this->save();
    }

    /**
     * Start the match (change status to in_progress).
     *
     * @return void
     */
    public function start(): void
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->save();
    }

    /**
     * Schedule the match for a specific time.
     *
     * @param \Carbon\Carbon $dateTime
     * @param int|null $tableNumber
     * @param int|null $refereeId
     * @return void
     */
    public function schedule(\Carbon\Carbon $dateTime, ?int $tableNumber = null, ?int $refereeId = null): void
    {
        $this->status = self::STATUS_SCHEDULED;
        $this->scheduled_at = $dateTime;
        if ($tableNumber) {
            $this->table_number = $tableNumber;
        }
        if ($refereeId) {
            $this->referee_id = $refereeId;
        }
        $this->save();
    }
}
