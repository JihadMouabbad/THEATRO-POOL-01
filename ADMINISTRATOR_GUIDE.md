# THEATRO POOL - ADMINISTRATOR GUIDE

## System Overview

Theatro Pool is a production-ready tournament management system for 8-ball pool competitions. This guide explains how to use the system as an administrator.

---

## GETTING STARTED

### Access Admin Features

1. Login with admin account
2. Navigate to `/dashboard`
3. Or go directly to `/tournaments`

### Key Features Available

- **Create Tournaments** - Set up new competitions
- **Register Players** - Add players to tournaments
- **Generate Brackets** - Auto-create match structures
- **Record Results** - Input match scores
- **Override Results** - Fix incorrect scores (with audit trail)
- **Schedule Matches** - Assign tables, times, referees
- **Live Mode** - Full-screen scoreboard display
- **View Analytics** - Standings, player stats, history

---

## TOURNAMENT LIFECYCLE

### Step 1: Create Tournament

```
Navigate: /tournaments/create

Fill in:
- Tournament Name (e.g., "Spring Qualifier")
- Location (e.g., "Downtown Billiards")
- Start Date & Time
- Max Players (8, 16, 32, 64, or 128)
- Bracket Type:
  * Single Elimination (fastest, standard)
  * Double Elimination (fairest, allows second chance)
  * Round-Robin (every player plays everyone)

Status: UPCOMING
```

### Step 2: Register Players

```
Navigate: /tournaments/{id}

Click: "Manage Players"

- Search for players
- Add players one by one
- Cannot exceed max_players
- Players auto-seeded by registration order
- Can reorder seeds before bracket generation
- Remove players (only when UPCOMING)
```

### Step 3: Validate Registration

```
Requirements:
✓ All seats filled (must equal max_players)
✓ Players properly seeded
✓ No duplicate players

Status must be: UPCOMING
Only then: [Generate Bracket] button appears
```

### Step 4: Generate Bracket

```
Navigate: /tournaments/{id}

Click: [Generate Bracket]

System automatically:
✓ Creates all matches for all rounds
✓ Assigns players to first round
✓ Links winners to next round
✓ Sets tournament status to ONGOING
✓ Shows bracket visualization
```

### Step 5: Play Matches

```
Navigate: /tournaments/{id}

Bracket shows:
- All rounds
- All matches
- Player names
- Match status (pending, in progress, completed)

Click match to:
- View details
- Record score
- View live scoreboard
- Schedule (set time/table/referee)
```

### Step 6: Complete Tournament

```
System auto-detects:
✓ When final match is completed
✓ Sets tournament status to FINISHED
✓ Names champion
✓ Locks results

Manual completion:
View /tournaments/{id} → Champion shows at top
```

---

## RECORDING MATCH RESULTS

### Normal Result Entry

```
1. Click match in bracket
2. Click [Edit Result] or [Record Score]
3. Enter scores:
   - Player 1 Score: (number)
   - Player 2 Score: (number)
   - Scores MUST be different (no ties)
4. Click [Record Result]

System automatically:
✓ Determines winner by higher score
✓ Updates player statistics
✓ Advances winner to next match
✓ Checks if tournament complete
```

### Admin Override

```
When: Score was recorded incorrectly

1. Click match
2. Click [Override]
3. Select correct winner from dropdown
4. Enter final scores
5. Check: "Mark as admin override"
6. Confirmation dialog appears
7. System:
   ✓ Reverses old statistics
   ✓ Records new result
   ✓ Marks is_admin_override = true
   ✓ Updates advancement
   ✓ Sets audit flag (visible in history)
```

---

## MATCH SCHEDULING

### Schedule a Match

```
1. Click match
2. Click [Schedule]
3. Enter:
   - Date & Time (e.g., "2025-02-15 14:30")
   - Table Number (1-99, optional)
   - Referee (select from players, optional)
4. Click [Schedule]

Match status: SCHEDULED
Players will see scheduled time (Phase 2: notifications)
```

### Start Live Match

```
When ready to play:

1. Click match
2. Click [Start Live Match]
3. Status: IN_PROGRESS

Then:
- Click [View Live] for full-screen scoreboard
- Updates every 3 seconds (auto-refresh)
- Used on TV/display at pool hall
```

---

## VIEWING TOURNAMENT DATA

### Bracket View

```
Navigate: /tournaments/{id}

Visual bracket showing:
- All rounds left-to-right
- Match cards with player names
- Scores (if completed)
- Status color-coded
  * Gray: Pending
  * Blue: Scheduled
  * Green: Completed

Click match for details
```

### Standings/Leaderboard

```
Navigate: /tournaments/{id}

Shows current rankings:
- Rank number
- Player name
- Wins
- Losses
- Win rate percentage

Sorted by:
1. Most wins
2. Highest win rate

Updates live as matches complete
```

### Match History

```
Navigate: /tournaments/{id} → Click standings row

Shows player's tournament record:
- All matches played
- Opponents
- Scores
- Results (Win/Loss)
- Match dates
```

---

## LIVE MATCH MODE

### Accessing Live Mode

```
Navigate: /tournaments/{id} → Click match → [View Live]

Or direct: /matches/{id}/live

Full-screen scoreboard showing:
- Player 1 name & score (large)
- Player 2 name & score (large)
- Table number
- Match format (e.g., "Race to 5")
- Referee name
- Auto-refreshes every 3 seconds
```

### Uses

1. **Pool Hall Display** - Mount on TV behind bar
2. **Spectator Viewing** - QR code to live match (Phase 2)
3. **Tournament Organization** - See all matches at glance
4. **Broadcasting** - Screen capture for stream/display

### Auto-Refresh Behavior

```
- Updates every 3 seconds
- No manual refresh needed
- Shows latest scores automatically
- Admin can update from admin panel
- Display updates in real-time
```

---

## COMMON ADMIN TASKS

### Task: View All Active Tournaments

```
GET /tournaments
Filter by Status: "ongoing"
Shows only tournaments currently playing
```

### Task: View Pending Matches

```
GET /tournaments/{id}/pending-matches
Shows matches ready to play (both players set)
Useful for scheduling
```

### Task: Get Player Statistics for Tournament

```
Navigate: /tournaments/{id}/standings
Or API: GET /tournaments/{id}/players/{playerId}/history

Shows:
- Win/loss record in this tournament
- All matches played
- Opponents
- Scores
```

### Task: Export Tournament Results

```
(Phase 2 feature - currently export via browser)

Print: Right-click → Print bracket
Export: Copy standings table → Excel
```

### Task: Retroactive Score Correction

```
If score entered wrong:
1. Navigate to match
2. Click [Override]
3. Re-enter correct scores
4. System reverses & re-records
5. Audit trail created
```

### Task: Change Tournament Settings

```
(Only if UPCOMING status)

1. Navigate: /tournaments/{id}/edit
2. Change allowed fields:
   - Name
   - Description
   - Start date
   - Location
   - Max players (↑ only)
3. Save

Note: Cannot change bracket_type after creation
(Must delete & recreate)
```

---

## TROUBLESHOOTING

### Problem: "Generate Bracket" Button Won't Appear

**Cause**: Tournament not full or not UPCOMING

**Solution**:

- Check player count = max_players
- Verify status is UPCOMING
- If status = ONGOING, bracket already generated

### Problem: "Both Players Must Be Set" Error

**Cause**: Bracket generation incomplete

**Solution**:

- Verify bracket generation completed successfully
- Check that players properly registered
- Try regenerating bracket

### Problem: Match Score Won't Save

**Cause**: Scores must be different (no ties allowed)

**Solution**:

- Enter different scores
- One player must have higher score
- If both scores should be same = invalid in pool

### Problem: Tournament Won't Auto-Complete

**Cause**: Final match not completed

**Solution**:

- Verify final round matches exist
- Record all final round match results
- System will auto-complete when last match finished

### Problem: Admin Override Not Working

**Cause**: Invalid winner selected

**Solution**:

- Verify selected winner is one of the two players
- Check player1_id and player2_id match
- Refresh page and try again

---

## API ENDPOINTS FOR INTEGRATION

### JSON Tournament Data

```bash
# Get tournament bracket
curl http://localhost/tournaments/1/bracket

# Get standings
curl http://localhost/tournaments/1/standings

# Get all matches
curl http://localhost/tournaments/1/matches

# Get pending matches only
curl http://localhost/tournaments/1/pending-matches
```

### JSON Match Data

```bash
# Get live match data
curl http://localhost/matches/5/data

# Get player history
curl http://localhost/tournaments/1/players/10/history
```

### Response Format

```json
{
    "bracket": {
        "1": [
            {
                "id": 1,
                "round": 1,
                "match_number": 1,
                "player1": { "id": 1, "name": "John", "score": 5 },
                "player2": { "id": 2, "name": "Jane", "score": 3 },
                "status": "completed",
                "winner_id": 1
            }
        ]
    },
    "standings": [
        {
            "rank": 1,
            "player": { "id": 1, "name": "John" },
            "wins": 3,
            "losses": 0,
            "win_rate": 100
        }
    ]
}
```

---

## KEYBOARD SHORTCUTS & TIPS

### Quick Navigation

- Press `G` → Go to tournament list
- Press `N` → Create new tournament
- Press `B` → View bracket
- Press `L` → View live mode

### Mouse Tips

- Hover match card → Shows details
- Right-click match → Context menu
- Double-click standings → Player profile
- Shift+Click → Multi-select (Phase 2)

---

## SECURITY NOTES

### Your Admin Credentials

- Keep password secure
- Don't share admin account
- Use strong password (8+ chars, mixed case, numbers)
- Change password monthly

### Audit Trail

- Admin overrides logged with flag: `is_admin_override = true`
- All match edits timestamped
- History preserved for review

### Data Backup

- Database backed up daily (check with IT)
- Never delete tournament unless absolutely necessary
- Deleted tournaments cannot be recovered

---

## ANALYTICS & REPORTS

### Standard Reports Available

1. **Player Rankings**
    - All-time ranking points
    - Tournament-specific stats
    - Monthly rankings (Phase 2)

2. **Tournament Results**
    - Final standings
    - Match history
    - Statistics per player

3. **Head-to-Head**
    - Player vs player record
    - Historical matchups
    - Performance trends (Phase 2)

### Future Reports (Phase 2)

- ELO rating progression
- Monthly leaderboards
- Performance analytics
- Win-loss trends

---

## PLAYER SUPPORT

### When Players Ask...

**"How do I register for a tournament?"**

- Upcoming tournaments visible at /tournaments
- Click [Join] button (auth required)
- Confirms registration immediately

**"Where's my tournament bracket?"**

- /tournaments/{id} shows bracket
- Bookmark for quick access
- Live updates as matches complete

**"Can I see the schedule?"**

- Bracket shows all round 1 matches
- Schedule appears when admin schedules
- (Phase 2: Email notifications)

**"Why did my score change?"**

- Check match details page
- Look for `is_admin_override` flag
- Admin may have corrected error
- Contact admin if incorrect

---

## MAINTENANCE TASKS

### Daily

- Monitor active tournaments
- Ensure matches recorded on time
- Check for pending matches

### Weekly

- Review player statistics
- Verify ranking accuracy
- Backup database

### Monthly

- Archive finished tournaments
- Generate usage reports
- Review system performance
- Reset monthly rankings (Phase 2)

### Quarterly

- Player statistics review
- Bracket format analysis
- Update rules if needed
- Performance optimization

---

## BEST PRACTICES

### Tournament Setup

✓ Set start date at least 1 week ahead
✓ Register players at least 2 days early
✓ Generate bracket day before
✓ Brief players on bracket type

### During Tournament

✓ Record scores immediately
✓ Use live mode for spectators
✓ Monitor match flow
✓ Address disputes quickly

### Scoring

✓ Double-check scores before saving
✓ Use override feature for corrections
✓ Keep audit trail clean
✓ Document controversial calls

### Player Management

✓ Keep photos updated
✓ Verify contact information
✓ Communicate changes promptly
✓ Maintain fair seeding

---

## CONTACT & SUPPORT

**Technical Issues**:

- See PHASE_1_QUICK_REFERENCE.md
- Check error messages carefully
- Report bugs with screenshots

**System Questions**:

- Review PHASE_1_DOCUMENTATION.md
- Check FAQ below

**Feature Requests**:

- Document in GitHub issues
- Prioritized for Phase 2+

---

## FAQ

**Q: Can I change bracket type after tournament starts?**
A: No. Delete and recreate if necessary.

**Q: What happens if someone doesn't show up?**
A: Admin override - set opponent as winner with 0 score for forfeit.

**Q: Can I pause and resume a tournament?**
A: Yes - set matches to pending, then schedule when ready.

**Q: What if there's a tie in wins?**
A: Standings sorted by wins, then win rate %. More detailed seeding in Phase 2.

**Q: How do I undo an override?**
A: Record result again with original scores (override flag remains).

**Q: Can I export results?**
A: Print/copy standings table. PDF export in Phase 2.

**Q: Are scores validated?**
A: Yes - must be different, must be 0-255.

**Q: Can I change player seeding after bracket creation?**
A: No - bracket locked after generation.

---

**Administrator Guide v1.0**
**Last Updated**: January 18, 2026
**Status**: Ready for use

For technical documentation, see companion files:

- PHASE_1_DOCUMENTATION.md
- PHASE_1_IMPLEMENTATION.md
- PHASE_1_QUICK_REFERENCE.md
