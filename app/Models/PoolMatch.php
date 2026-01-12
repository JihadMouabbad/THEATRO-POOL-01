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
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

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
     * Check if the match is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the match is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
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
}
