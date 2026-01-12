<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tournament model - represents a single-elimination pool tournament.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property string $location
 * @property int $max_players
 * @property string $status
 * @property int $total_rounds
 */
class Tournament extends Model
{
    use HasFactory;

    /**
     * Tournament status constants.
     */
    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_FINISHED = 'finished';

    /**
     * Allowed player counts for tournaments.
     */
    public const ALLOWED_PLAYER_COUNTS = [8, 16, 32];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'max_players',
        'status',
        'total_rounds',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_players' => 'integer',
        'total_rounds' => 'integer',
    ];

    /**
     * Get the players registered in this tournament.
     *
     * @return BelongsToMany<Player>
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'tournament_player')
            ->withPivot('seed', 'registered_at')
            ->orderBy('pivot_seed');
    }

    /**
     * Get all matches in this tournament.
     *
     * @return HasMany<PoolMatch>
     */
    public function matches(): HasMany
    {
        return $this->hasMany(PoolMatch::class)->orderBy('round')->orderBy('match_number');
    }

    /**
     * Get matches for a specific round.
     *
     * @param int $round
     * @return HasMany<PoolMatch>
     */
    public function matchesForRound(int $round): HasMany
    {
        return $this->hasMany(PoolMatch::class)->where('round', $round)->orderBy('match_number');
    }

    /**
     * Check if the tournament is upcoming.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_UPCOMING;
    }

    /**
     * Check if the tournament is ongoing.
     *
     * @return bool
     */
    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    /**
     * Check if the tournament is finished.
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Get the number of registered players.
     *
     * @return int
     */
    public function getRegisteredPlayersCountAttribute(): int
    {
        return $this->players()->count();
    }

    /**
     * Check if the tournament is full.
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->registered_players_count >= $this->max_players;
    }

    /**
     * Check if a player can be registered.
     *
     * @return bool
     */
    public function canRegisterPlayer(): bool
    {
        return $this->isUpcoming() && !$this->isFull();
    }

    /**
     * Get the round name (e.g., "Quarter Finals", "Semi Finals", "Final").
     *
     * @param int $round
     * @return string
     */
    public function getRoundName(int $round): string
    {
        $roundsRemaining = $this->total_rounds - $round + 1;
        
        return match ($roundsRemaining) {
            1 => 'Final',
            2 => 'Semi Finals',
            3 => 'Quarter Finals',
            4 => 'Round of 16',
            5 => 'Round of 32',
            default => "Round $round",
        };
    }

    /**
     * Get the champion (winner of the final match).
     *
     * @return Player|null
     */
    public function getChampion(): ?Player
    {
        if (!$this->isFinished()) {
            return null;
        }

        $finalMatch = $this->matches()
            ->where('round', $this->total_rounds)
            ->first();

        return $finalMatch?->winner;
    }
}
