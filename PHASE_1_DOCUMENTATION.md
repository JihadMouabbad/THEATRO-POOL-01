# Theatro Pool - Tournament Management System (Phase 1)

## Architecture Overview

This document outlines the production-ready tournament management system for 8-ball pool competitions. Phase 1 covers core tournament functionality with support for multiple bracket formats, match management, and basic player statistics.

---

## DATABASE SCHEMA

### Core Tables

#### `tournaments`

```sql
id (primary key)
name (string)
description (text, nullable)
start_date (date)
end_date (date, nullable)
location (string)
max_players (integer)
status (enum: upcoming, ongoing, finished)
bracket_type (enum: single_elimination, double_elimination, round_robin)
total_rounds (integer)
champion_id (foreign key → players, nullable)
created_at, updated_at
```

**Indexes**: `(start_date)`, `(status)`, `(champion_id)`

#### `players`

```sql
id (primary key)
name (string)
nickname (string, nullable)
email (string, unique, nullable)
phone (string, nullable)
photo_path (string, nullable)
wins (unsigned integer, default: 0)
losses (unsigned integer, default: 0)
total_matches (unsigned integer, default: 0)
ranking_points (unsigned integer, default: 1000)
notes (text, nullable)
created_at, updated_at
```

**Indexes**: `(name)`, `(ranking_points)`, `(wins)`

#### `matches`

```sql
id (primary key)
tournament_id (foreign key → tournaments, cascade delete)
round (unsigned tinyint)
match_number (unsigned tinyint)
player1_id (foreign key → players, nullable)
player2_id (foreign key → players, nullable)
player1_score (unsigned tinyint, nullable)
player2_score (unsigned tinyint, nullable)
winner_id (foreign key → players, nullable)
next_match_id (unsigned integer, nullable)
status (enum: pending, scheduled, in_progress, completed)
scheduled_at (timestamp, nullable)
table_number (unsigned tinyint, nullable)
referee_id (foreign key → players, nullable)
match_format (enum: race_to, best_of, default: race_to)
frames_to_win (unsigned tinyint, default: 5)
is_admin_override (boolean, default: false)
completed_at (timestamp, nullable)
created_at, updated_at
```

**Indexes**: `(tournament_id, round)`, `(tournament_id, status)`, `(winner_id)`, `(table_number)`, `(referee_id)`

#### `tournament_player` (Pivot)

```sql
tournament_id (foreign key)
player_id (foreign key)
seed (unsigned integer)
registered_at (timestamp)
primary key: (tournament_id, player_id)
```

---

## DOMAIN MODELS

### Tournament Model

- **Relationships**:
    - `players()` - many-to-many with pivot data
    - `matches()` - one-to-many
    - `champion()` - belongs to Player
- **Constants**: Status and bracket types
- **Methods**:
    - `isUpcoming()`, `isOngoing()`, `isFinished()`
    - `isFull()`, `canRegisterPlayer()`
    - `getChampion()`, `getRoundName(round)`
    - `getBracketTypes()`, `supportsBracketType(type)`

### Player Model

- **Relationships**:
    - `tournaments()` - many-to-many
    - `matchesAsPlayer1()`, `matchesAsPlayer2()`
    - `wonMatches()`, `championedTournaments()`
- **Methods**:
    - `recordWin()`, `recordLoss()` - auto-increment stats
    - `getDisplayNameAttribute()` - returns nickname or name
    - `getWinRateAttribute()` - calculated percentage

### PoolMatch Model

- **Relationships**:
    - `tournament()` - belongs to Tournament
    - `player1()`, `player2()` - belongs to Player
    - `winner()` - belongs to Player
    - `referee()` - belongs to Player
    - `nextMatch()` - belongs to PoolMatch (self-referencing)
- **Constants**: Status and format types
- **Methods**:
    - Status checkers: `isPending()`, `isScheduled()`, `isInProgress()`, `isCompleted()`
    - `hasBothPlayers()`, `isReadyToComplete()`
    - `determineWinner()` - calculates winner from scores
    - `recordResult(p1Score, p2Score, isOverride)` - atomic result recording
    - `start()`, `schedule(dateTime, tableNumber, refereeId)`

---

## SERVICE LAYER

### BracketGenerator Service

Handles all bracket generation logic with support for three formats:

#### Single Elimination

- Requires power-of-2 player counts (8, 16, 32, 64, etc.)
- Generates complete bracket structure upfront
- Each match links to next match via `next_match_id`
- Automatic winner advancement

#### Double Elimination

- Winners bracket (single elimination style)
- Losers bracket created dynamically as players are eliminated
- Grand final between winners bracket champion and losers bracket champion
- **Note**: Phase 2 feature - foundation in place

#### Round-Robin

- Every player plays every other player once
- No elimination, pure win/loss record
- Multiple rounds with rotating schedule
- Rankings based on total wins

**Key Methods**:

- `generate(tournament)` - main entry point
- `processMatchResult(match, p1Score, p2Score, isOverride)`
- Protected helpers for each bracket type

### MatchManager Service

Orchestrates all match-related business logic:

**Result Recording**:

- `recordResult(match, p1Score, p2Score)` - normal result
- `overrideResult(match, winnerId, p1Score, p2Score)` - admin override
- Validates both players assigned before recording
- Auto-advances winner to next match
- Updates player statistics atomically

**Match Operations**:

- `scheduleMatch(match, dateTime, tableNumber, refereeId)`
- `startMatch(match)` - transitions to in_progress
- `advanceWinner(match)` - moves winner to next round
- `checkTournamentCompletion(tournament)` - auto-finish logic

**Queries**:

- `getPendingMatches(tournament)` - ready to play
- `getScheduledMatches(tournament)` - has schedule
- `getCompletedMatches(tournament)` - finished

**Analytics**:

- `getPlayerStats(tournament, player)` - wins, losses, match count, win rate
- `getTournamentStandings(tournament)` - leaderboard (sorted by wins then win rate)
- `getBracketData(tournament)` - structured bracket visualization data

**Safety**:

- All database operations wrapped in transactions
- Validation of player assignments before modifications
- Statistics reversal for admin overrides

---

## CONTROLLERS

### TournamentController

**Routes**:

```
GET    /tournaments                - list tournaments
GET    /tournaments/create         - create form
POST   /tournaments                - store new
GET    /tournaments/{id}           - show details
GET    /tournaments/{id}/edit      - edit form
PUT    /tournaments/{id}           - update
DELETE /tournaments/{id}           - delete

POST   /tournaments/{id}/register  - register player
DELETE /tournaments/{id}/players/{id} - unregister
POST   /tournaments/{id}/generate-bracket - start tournament

GET    /tournaments/{id}/bracket   - JSON bracket data
GET    /tournaments/{id}/standings - JSON leaderboard
```

**Key Features**:

- Prevent editing/deleting ongoing tournaments
- Auto-seat full tournaments
- Bracket generation validation
- JSON APIs for real-time updates

### MatchController

**Routes**:

```
GET    /matches/{id}               - show match
GET    /matches/{id}/edit          - result form
POST   /matches/{id}/result        - record result
POST   /matches/{id}/override      - admin override
POST   /matches/{id}/schedule      - schedule match
POST   /matches/{id}/start         - start live mode
GET    /matches/{id}/live          - live mode view
GET    /matches/{id}/data          - JSON live data

GET    /tournaments/{id}/matches   - all matches (JSON)
GET    /tournaments/{id}/pending   - pending only (JSON)
GET    /tournaments/{id}/players/{id}/history - player history (JSON)
```

**Key Features**:

- Live match scoring
- Admin result overrides with audit trail
- Match scheduling with tables and referees
- Full-screen "TV Mode" display
- Real-time JSON APIs for live updates

---

## BRACKET TYPES EXPLAINED

### Single Elimination

```
Round 1          Round 2          Final
Match 1: P1 vs P2 --┐
                    Match 5: W1 vs W3 --┐
Match 2: P3 vs P4 --┤                    Match 9: Champion
                    Match 6: W2 vs W4 --┘
Match 3: P5 vs P6 --┐
                    Match 7: W5 vs W7 --┐
Match 4: P7 vs P8 --┤                    Match 10
                    Match 8: W6 vs W8 --┘
```

- 8 players = 3 rounds, 7 total matches
- 16 players = 4 rounds, 15 total matches
- Fast-paced, clear advancement path

### Double Elimination

- Winners Bracket: Single elimination (faster path)
- Losers Bracket: Consolation bracket for first-round losers
- Grand Final: Winners bracket champion vs. Losers bracket champion
- **Ensures**: Every player gets minimum 2 matches
- **Total rounds** = 2 × log₂(n) + 1

### Round-Robin

- Every player plays every other player exactly once
- No eliminations
- Final standings based on total wins
- Best for skill assessment
- **Matches needed**: n(n-1)/2
- **Example**: 8 players = 28 total matches across 7 rounds

---

## API ENDPOINTS (JSON)

### Tournament Data

```
GET /api/tournaments/{id}/bracket
Response:
{
  "bracket": {
    "1": [ { id, round, match_number, player1, player2, status, ... } ],
    "2": [ ... ],
    ...
  },
  "standings": [
    { rank, player, wins, losses, win_rate },
    ...
  ]
}
```

### Live Match Updates

```
GET /api/matches/{id}/data
Response:
{
  "id": 123,
  "round": 2,
  "player1": { "id": 1, "name": "John", "score": 5 },
  "player2": { "id": 2, "name": "Jane", "score": 3 },
  "status": "in_progress",
  "table_number": 5,
  "referee": "Bob Smith"
}
```

### Player History

```
GET /api/tournaments/{id}/players/{playerId}/history
Response:
{
  "stats": {
    "wins": 3,
    "losses": 1,
    "total_matches": 4,
    "win_rate": 75
  },
  "matches": [
    { "round": 1, "opponent": "Jane", "result": "Win", "score": "5-2" }
  ]
}
```

---

## BUSINESS RULES

### Tournament Registration

1. Tournament must be UPCOMING status
2. Cannot exceed max_players
3. Same player cannot register twice
4. Players automatically seeded in registration order
5. Seed can be manually adjusted before bracket generation

### Bracket Generation

1. Can only be done once, on UPCOMING tournaments
2. Transitions tournament to ONGOING
3. Single elimination requires power-of-2 player counts
4. All registered players must be included
5. Creates all matches upfront (no lazy generation)

### Match Results

1. Both players must be assigned before scoring
2. Scores must be different (no ties in pool)
3. Automatic winner determination by score
4. Winner automatically advances to next match
5. Player statistics updated atomically

### Admin Overrides

1. Can override any match result after it's completed
2. Creates audit trail via `is_admin_override` flag
3. Reverses old player statistics
4. Records new statistics
5. Winner advancement re-executed

### Tournament Completion

1. Auto-detected when final match is completed
2. Status changes to FINISHED
3. Champion automatically set to final match winner
4. End date set to completion timestamp

---

## VALIDATION RULES

### Tournament Creation

- `name`: required, string, max 255 chars
- `description`: optional, string, max 1000 chars
- `start_date`: required, date, must be today or later
- `location`: required, string, max 255 chars
- `max_players`: required, integer, one of [8, 16, 32, 64, 128]
- `bracket_type`: required, one of [single_elimination, double_elimination, round_robin]

### Player Registration

- `player_id`: required, must exist in players table
- Cannot be already registered in tournament
- Tournament must not be full
- Tournament must be UPCOMING

### Match Result

- `player1_score`: required, integer, 0-255
- `player2_score`: required, integer, 0-255, must differ from player1_score
- Both players must be assigned
- Match must not already be completed (unless admin override)

### Match Schedule

- `scheduled_at`: required, ISO datetime
- `table_number`: optional, integer, 1-99
- `referee_id`: optional, must exist in players table

---

## FUTURE ENHANCEMENTS (PHASE 2+)

### ELO Rating System

- Update player `ranking_points` after each match
- K-factor based on tournament prestige
- Monthly reset option
- Historical ratings tracking

### Rules Engine

- Pre-defined rule presets (APA, VNEA, etc.)
- Admin can override rules per tournament
- Auto-enforce foul penalties
- Break-off advantage tracking

### Double Elimination Completion

- Auto-generate losers bracket matches
- Proper bracket ordering and advancement
- Grand final (potential double-header)

### Notifications

- Email player when match scheduled
- Notify when opponent drops out
- Tournament start reminder
- Match result notifications

### Media

- Player photo upload/storage
- Match photos/video storage
- Tournament photos gallery

### Advanced Features

- Seeding by ranking points
- Handicap bracket support
- Team tournaments
- Custom player fields (hometown, skill level, etc.)
- Spectator mode with real-time updates
- Mobile app API

---

## TESTING STRATEGY

### Unit Tests

- Model methods: `recordWin()`, `determineWinner()`, etc.
- Service logic: bracket generation, score calculations

### Feature Tests

- Tournament CRUD operations
- Player registration/unregistration
- Bracket generation with validation
- Match result recording and advancement
- Admin overrides and statistics reversal

### Integration Tests

- Complete tournament lifecycle
- Winner advancement through all rounds
- Tournament completion detection
- API endpoints with authentication

---

## DEPLOYMENT NOTES

### Migrations

1. Run all three new migrations after pulling code
2. Order: bracket_type → match_details → player_profile
3. No data loss for existing records
4. Add default values for new nullable columns

### Service Container

- Register services in `AppServiceProvider`
- Auto-inject into controllers
- Singleton for stateless services

### Environment Variables

- No new environment variables required for Phase 1
- Photo upload path configuration (Phase 2)

---

## SECURITY CONSIDERATIONS

1. **Authorization**: Only admins can override results and delete tournaments
2. **Validation**: All user input validated before processing
3. **Transactions**: Critical operations wrapped in database transactions
4. **Audit Trail**: Admin overrides flagged with `is_admin_override`
5. **Data Integrity**: Foreign key constraints enforced at database level

---

## PERFORMANCE OPTIMIZATION

### Database

- Strategic indexes on frequently queried columns
- Eager loading in controllers to prevent N+1 queries
- Pagination for large lists

### Caching (Future)

- Cache tournament standings (invalidate on match completion)
- Cache player statistics per tournament
- Cache bracket structure

### API

- JSON responses include only necessary fields
- Lazy-loading of related records
- Rate limiting on public endpoints (Phase 2)

---

## CODE QUALITY

### Standards

- PSR-12 PHP code style
- Type hints on all methods
- PHPDoc comments for public APIs
- Consistent naming conventions

### Testing Coverage Target

- 80%+ for services
- 60%+ for controllers
- 90%+ for critical models

---

**Status**: Phase 1 complete (Core functionality)
**Next**: Phase 2 (ELO ratings, rules engine, notifications)
**Last Updated**: January 18, 2026
