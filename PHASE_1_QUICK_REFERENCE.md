# THEATRO POOL - QUICK REFERENCE GUIDE

## 🚀 Getting Started

### Setup

```bash
cd c:\Users\Mouabbad\Desktop\XX\THEATRO-POOL

# Run migrations (if not done)
php artisan migrate

# Start development server
php artisan serve

# In another terminal, start frontend dev
npm run dev
```

### Test the System

**In Laravel Tinker**:

```php
php artisan tinker

# Create a test tournament
$tournament = Tournament::create([
    'name' => 'Test Tournament',
    'start_date' => now(),
    'location' => 'Downtown Billiards',
    'max_players' => 8,
    'bracket_type' => 'single_elimination',
    'status' => 'upcoming',
]);

# Register players
$players = Player::limit(8)->get();
$tournament->players()->attach($players->mapWithKeys(fn($p, $i) => [$p->id => ['seed' => $i + 1]])->toArray());

# Generate bracket
app(BracketGenerator::class)->generate($tournament);

# View bracket
GET /tournaments/1/bracket

# Record a result
$match = $tournament->matches()->first();
app(MatchManager::class)->recordResult($match, 5, 3);
```

---

## 📋 File Structure

### Core Services (Business Logic)

```
app/Services/
├── BracketGenerator.php     ← Bracket creation & advancement
└── MatchManager.php         ← Match operations, stats, analytics
```

### Controllers (Request Handlers)

```
app/Http/Controllers/
├── TournamentController.php  ← CRUD + JSON APIs
└── MatchController.php       ← Match mgmt + JSON APIs
```

### Models (Database)

```
app/Models/
├── Tournament.php
├── PoolMatch.php
└── Player.php
```

### Database

```
database/migrations/
├── 2024_01_02_000001_add_bracket_type_to_tournaments_table.php
├── 2024_01_02_000002_add_match_details_to_matches_table.php
└── 2024_01_02_000003_add_player_profile_fields.php
```

---

## 🔑 Key Methods

### BracketGenerator

```php
$generator = app(BracketGenerator::class);

// Generate bracket (must be UPCOMING, all players registered)
$generator->generate($tournament);

// Process match result
$generator->processMatchResult($match, $player1Score, $player2Score);
```

### MatchManager

```php
$manager = app(MatchManager::class);

// Record result
$manager->recordResult($match, 5, 3);

// Override result (admin)
$manager->overrideResult($match, $winnerId, 5, 3);

// Schedule match
$manager->scheduleMatch($match, $dateTime, $tableNumber, $refereeId);

// Get analytics
$standings = $manager->getTournamentStandings($tournament);  // array of rank data
$stats = $manager->getPlayerStats($tournament, $player);    // {wins, losses, total, win_rate}
$bracket = $manager->getBracketData($tournament);           // structured bracket by round

// Get queries
$pending = $manager->getPendingMatches($tournament);        // ready to play
$scheduled = $manager->getScheduledMatches($tournament);    // scheduled
$completed = $manager->getCompletedMatches($tournament);    // finished
```

### Tournament Model

```php
$tournament->isUpcoming()      // status check
$tournament->isOngoing()       // status check
$tournament->isFinished()      // status check
$tournament->canRegisterPlayer() // checks upcoming + not full
$tournament->isFull()          // player count == max_players
$tournament->getChampion()     // returns champion Player
$tournament->getRoundName(2)   // "Semi Finals"
Tournament::getBracketTypes()  // returns array of types
```

### PoolMatch Model

```php
$match->isPending()       // status check
$match->isScheduled()     // status check
$match->isInProgress()    // status check
$match->isCompleted()     // status check
$match->hasBothPlayers()  // checks if both player IDs set
$match->isReadyToComplete() // checks both scores entered
$match->determineWinner()   // returns winning player ID
$match->recordResult($p1Score, $p2Score, $isOverride)
$match->start()           // changes to in_progress
$match->schedule($dateTime, $tableNumber, $refereeId)
$match->getScoreDisplayAttribute() // returns "5-3" or "-"
```

### Player Model

```php
$player->recordWin()      // increments wins & total_matches
$player->recordLoss()     // increments losses & total_matches
$player->getDisplayNameAttribute() // returns nickname ?? name
$player->getWinRateAttribute()     // returns percentage
```

---

## 🛣️ API Endpoints

### Tournament APIs

```
GET  /tournaments                          - List all
GET  /tournaments/{id}                     - Details
GET  /tournaments/{id}/bracket             - Bracket JSON
GET  /tournaments/{id}/standings           - Leaderboard JSON
GET  /tournaments/{id}/matches             - All matches JSON
GET  /tournaments/{id}/pending-matches     - Pending only JSON
POST /tournaments/{id}/generate-bracket    - Create bracket
```

### Match APIs

```
GET  /matches/{id}                         - Match details
GET  /matches/{id}/data                    - Match JSON (live)
GET  /matches/{id}/live                    - Live mode (full-screen)
POST /matches/{id}/override                - Admin override
POST /matches/{id}/schedule                - Schedule match
POST /matches/{id}/start                   - Start (in_progress)
GET  /tournaments/{id}/players/{pid}/history - Player history JSON
```

---

## 📊 Bracket Types

### Single Elimination

- **Requirements**: Power-of-2 players (8, 16, 32, ...)
- **Rounds**: log₂(n)
- **Total Matches**: n - 1
- **Example**: 8 players = 3 rounds = 7 matches
- **Speed**: Fast
- **Best for**: Quick tournaments

### Double Elimination (Phase 2)

- **Requirements**: Any player count
- **Structure**: Winners bracket + Losers bracket + Grand Final
- **Total Matches**: 2n - 1 (approximately)
- **Speed**: Slow
- **Best for**: Fair tournament (everyone plays min 2 matches)

### Round-Robin

- **Requirements**: Any player count
- **Structure**: Every player plays every other player once
- **Total Matches**: n(n-1)/2
- **Rounds**: n - 1
- **Example**: 8 players = 7 rounds = 28 matches
- **Speed**: Slowest
- **Best for**: Skill assessment, balanced comparison

---

## 🔐 Authorization Rules

### Admin-Only Operations

- Create/edit/delete tournaments
- Register/unregister players
- Generate bracket
- Override match results
- Schedule matches
- Start matches

### Player Operations (Auth Required)

- Self-register to upcoming tournaments
- Self-leave tournaments (only if UPCOMING)
- View own tournament history
- View own profile

### Public Operations (No Auth)

- View tournaments list
- View tournament details/bracket
- View player profiles
- View rankings
- View tournament standings

---

## ⚠️ Validation Rules

### Tournament Creation

```php
'name' => 'required|string|max:255',
'start_date' => 'required|date|after_or_equal:today',
'location' => 'required|string|max:255',
'max_players' => 'required|in:8,16,32,64,128',
'bracket_type' => 'required|in:single_elimination,double_elimination,round_robin',
```

### Match Result

```php
'player1_score' => 'required|integer|min:0|max:255',
'player2_score' => 'required|integer|min:0|max:255|different:player1_score',
```

### Match Schedule

```php
'scheduled_at' => 'required|date_format:Y-m-d H:i',
'table_number' => 'nullable|integer|min:1|max:99',
'referee_id' => 'nullable|exists:players,id',
```

---

## 🐛 Common Issues & Solutions

### Bracket Generation Fails

**Error**: "Player count must be a power of 2"
**Cause**: Single elimination requires 8, 16, 32, or 64 players (not 7, 9, 15, etc.)
**Solution**: Use Round-Robin bracket type, or use Double Elimination (Phase 2)

### Match Won't Save Result

**Error**: "Both players must be set before updating match result"
**Cause**: Player 1 or Player 2 is null
**Solution**: Bracket not properly generated; check that bracket generation completed

### Admin Override Doesn't Work

**Error**: "Winner must be one of the match players"
**Cause**: Invalid winner_id provided
**Solution**: Ensure winner_id is either player1_id or player2_id

### Tournament Won't Auto-Complete

**Issue**: Tournament status still "ongoing" after final match
**Cause**: Final match not marked as completed
**Solution**: Record the final match result via admin panel or ensure it's round == total_rounds

---

## 📈 Performance Tips

1. **Eager Load Relations** (already done in controllers)

    ```php
    $tournament->load('players', 'matches.player1', 'matches.player2');
    ```

2. **Cache Standings** (future)

    ```php
    Cache::remember("tournament.{$id}.standings", 5 * 60, fn() => {
        return $manager->getTournamentStandings($tournament);
    });
    ```

3. **Use Select** for large datasets
    ```php
    $matches = $tournament->matches()
        ->select('id', 'round', 'match_number', 'player1_id', 'player2_id', 'winner_id')
        ->with('player1:id,display_name', 'player2:id,display_name')
        ->get();
    ```

---

## 🧪 Testing Checklist

- [ ] Create tournament with 8 players
- [ ] Generate single elimination bracket
- [ ] Record first round results
- [ ] Verify winner advancement
- [ ] Verify standings update
- [ ] Complete tournament
- [ ] Verify champion set
- [ ] Create 6-player round-robin
- [ ] Verify all-play-all pairings
- [ ] Admin override a result
- [ ] Verify statistics reversed and re-added
- [ ] Schedule a match
- [ ] View live match mode

---

## 📚 Documentation Files

| File                         | Purpose                                                                |
| ---------------------------- | ---------------------------------------------------------------------- |
| `PHASE_1_DOCUMENTATION.md`   | Complete architectural docs (DB schema, models, services, controllers) |
| `PHASE_1_IMPLEMENTATION.md`  | Implementation checklist and summary                                   |
| `PHASE_1_UI_UX_SPEC.md`      | UI/UX wireframes and design specs                                      |
| `PHASE_1_QUICK_REFERENCE.md` | This file - quick lookup                                               |

---

## 🔗 Related Repositories

- Phase 1 PR: https://github.com/JihadMouabbad/THEATRO-POOL/pull/1
- Main Branch: https://github.com/JihadMouabbad/THEATRO-POOL/

---

## 💡 Next Phase (Phase 2) Features

- [ ] ELO rating system (update ranking_points after matches)
- [ ] Rules engine with presets
- [ ] Double elimination losers bracket automation
- [ ] Email/SMS notifications
- [ ] Mobile app API
- [ ] WebSocket real-time updates
- [ ] Tournament seeding by ranking
- [ ] Monthly reset option
- [ ] Player photo upload
- [ ] Match photos/videos
- [ ] Team tournaments
- [ ] Handicap brackets

---

**Last Updated**: January 18, 2026
**Version**: 1.0
**Status**: Phase 1 ✅ Complete
