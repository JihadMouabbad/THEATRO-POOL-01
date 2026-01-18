<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property string $bracket_type
 * @property int $total_rounds
 * @property int|null $champion_id
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
     * Bracket type constants.
     */
    public const BRACKET_SINGLE_ELIMINATION = 'single_elimination';
    public const BRACKET_DOUBLE_ELIMINATION = 'double_elimination';
    public const BRACKET_ROUND_ROBIN = 'round_robin';

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
        'bracket_type',
        'total_rounds',
        'champion_id',
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
     * Get the champion of this tournament.
     *
     * @return BelongsTo<Player, $this>
     */
    public function champion(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'champion_id');
    }

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
        return $this->champion;
    }

    /**
     * Get all available bracket types.
     *
     * @return array
     */
    public static function getBracketTypes(): array
    {
        return [
            self::BRACKET_SINGLE_ELIMINATION => 'Single Elimination',
            self::BRACKET_DOUBLE_ELIMINATION => 'Double Elimination',
            self::BRACKET_ROUND_ROBIN => 'Round Robin',
        ];
    }

    /**
     * Check if tournament supports the given bracket type.
     *
     * @param string $type
     * @return bool
     */
    public function supportsBracketType(string $type): bool
    {
        return in_array($type, [
            self::BRACKET_SINGLE_ELIMINATION,
            self::BRACKET_DOUBLE_ELIMINATION,
            self::BRACKET_ROUND_ROBIN,
        ]);
    }
}
