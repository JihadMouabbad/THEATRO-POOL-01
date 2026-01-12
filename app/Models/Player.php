<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Player model - represents a billiard player in the system.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $nickname
 * @property string|null $email
 * @property string|null $phone
 * @property int $wins
 * @property int $losses
 * @property int $total_matches
 * @property string|null $notes
 */
class Player extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'phone',
        'wins',
        'losses',
        'total_matches',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'wins' => 'integer',
        'losses' => 'integer',
        'total_matches' => 'integer',
    ];

    /**
     * Get the tournaments this player is registered in.
     *
     * @return BelongsToMany<Tournament>
     */
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, 'tournament_player')
            ->withPivot('seed', 'registered_at')
            ->withTimestamps();
    }

    /**
     * Get all matches where this player participated as player 1.
     *
     * @return HasMany<PoolMatch>
     */
    public function matchesAsPlayer1(): HasMany
    {
        return $this->hasMany(PoolMatch::class, 'player1_id');
    }

    /**
     * Get all matches where this player participated as player 2.
     *
     * @return HasMany<PoolMatch>
     */
    public function matchesAsPlayer2(): HasMany
    {
        return $this->hasMany(PoolMatch::class, 'player2_id');
    }

    /**
     * Get all matches won by this player.
     *
     * @return HasMany<PoolMatch>
     */
    public function wonMatches(): HasMany
    {
        return $this->hasMany(PoolMatch::class, 'winner_id');
    }

    /**
     * Get all tournaments where this player was the champion.
     *
     * @return HasMany<Tournament>
     */
    public function championedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'champion_id');
    }

    /**
     * Calculate the win rate percentage.
     *
     * @return float
     */
    public function getWinRateAttribute(): float
    {
        if ($this->total_matches === 0) {
            return 0.0;
        }
        return round(($this->wins / $this->total_matches) * 100, 1);
    }

    /**
     * Get the display name (nickname or name).
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?? $this->name;
    }

    /**
     * Increment wins and update total matches.
     *
     * @return void
     */
    public function recordWin(): void
    {
        $this->increment('wins');
        $this->increment('total_matches');
    }

    /**
     * Increment losses and update total matches.
     *
     * @return void
     */
    public function recordLoss(): void
    {
        $this->increment('losses');
        $this->increment('total_matches');
    }
}
