# THEATRO POOL - UI/UX SPECIFICATIONS (Phase 1)

## Design Philosophy

The interface prioritizes **clarity, speed, and real-time visibility** for pool hall operators and players. Admin dashboards are feature-rich; player views are simple and engaging.

---

## 1. TOURNAMENT VIEWS

### Tournament List Page (`/tournaments`)

**Purpose**: Browse all tournaments, filter by status

**Layout**:

```
┌─────────────────────────────────────────┐
│ TOURNAMENTS                             │
├─────────────────────────────────────────┤
│ [Search] [Filter: Status ▼]             │
├─────────────────────────────────────────┤
│ Tournament Name          Status   Players│
├─────────────────────────────────────────┤
│ Regional Qualifier      UPCOMING  8/16  │
│ Spring Championship     ONGOING   8/8   │
│ Last Month Final        FINISHED  8/8   │
└─────────────────────────────────────────┘
```

**Features**:

- Search by name/location
- Filter by status (UPCOMING, ONGOING, FINISHED)
- Sort by date
- Display: name, status badge, player count, start date
- Action: click row to view details

### Tournament Details Page (`/tournaments/{id}`)

**Purpose**: Tournament administration and live viewing

**Layout** (Two-Column Grid):

```
┌──────────────────────┬──────────────────┐
│  LEFT: BRACKET VIEW  │  RIGHT: SIDEBAR  │
├──────────────────────┼──────────────────┤
│                      │ Tournament Info  │
│  Round 1             │ ─────────────────│
│  ┌────────────────┐  │ Name: Regional   │
│  │ John vs Jane   │  │ Status: ONGOING  │
│  │ 5 - 3 ✓        │  │ Players: 8/8     │
│  └────────────────┘  │ Bracket: Single  │
│                      │ Rounds: 3        │
│  ┌────────────────┐  │                  │
│  │ Bob vs Tom     │  │ Standings        │
│  │ Pending...     │  │ ─────────────────│
│  └────────────────┘  │ 1. John    2-0   │
│                      │ 2. Jane    1-1   │
│                      │ 3. Bob     1-1   │
│                      │ 4. Tom     0-2   │
│                      │                  │
│  Round 2             │ Actions          │
│  ┌────────────────┐  │ ─────────────────│
│  │ John vs Tom    │  │ [Edit] [Bracket] │
│  │ Pending...     │  │                  │
│  └────────────────┘  │ [Live Mode]      │
│                      │                  │
└──────────────────────┴──────────────────┘
```

**Components**:

1. **Bracket Visualization**:
    - Visual bracket tree showing all rounds
    - Color-coded match status (pending: gray, in-progress: blue, completed: green)
    - Player names and scores in match cards
    - Click match to view/edit details
    - Responsive layout (collapses on mobile)

2. **Tournament Info Sidebar**:
    - Title, status badge, player count
    - Bracket type and total rounds
    - Start/end dates
    - Location
    - Champion (when finished)
    - Edit button (admin only, upcoming only)
    - Delete button (admin only, upcoming only)

3. **Standings Table**:
    - Rank, player name, wins, losses, win rate %
    - Sortable columns
    - Color highlight: top 3 in gold, silver, bronze
    - (Future: photo thumbnails)

4. **Action Buttons**:
    - **[Generate Bracket]** - appears when tournament is UPCOMING and full
    - **[Live Mode]** - appears when tournament is ONGOING
    - **[Print Bracket]** - admin only
    - **[Export Results]** - admin only

### Tournament Create/Edit Page (`/tournaments/create`, `/tournaments/{id}/edit`)

**Purpose**: Create new tournament or modify settings

**Form**:

```
┌─────────────────────────────────────────┐
│ CREATE TOURNAMENT                       │
├─────────────────────────────────────────┤
│ Tournament Name *                       │
│ [Regional Qualifier            ]        │
│                                         │
│ Description                             │
│ [One-day qualifier tournament   ]        │
│ [for the Spring Championship    ]        │
│                                         │
│ Location *                              │
│ [Downtown Billiards             ]        │
│                                         │
│ Start Date & Time *                     │
│ [2025-02-15] [10:00]                    │
│                                         │
│ Max Players *                           │
│ [16           ▼]                        │
│ (8, 16, 32, 64, 128)                   │
│                                         │
│ Bracket Type *                          │
│ ◉ Single Elimination                    │
│ ○ Double Elimination                    │
│ ○ Round-Robin                           │
│                                         │
│                    [Cancel] [Create]    │
└─────────────────────────────────────────┘
```

**Features**:

- Input validation with error messages
- Bracket type explanation tooltip
- Date/time pickers
- Read-only fields when editing (for started tournaments)
- Confirmation dialog before updating started tournament

---

## 2. PLAYER REGISTRATION

### Player List & Registration Modal (`/tournaments/{id}`)

**Purpose**: Register/unregister players for tournament

**Modal**:

```
┌─────────────────────────────────┐
│ Register Players                │
├─────────────────────────────────┤
│ [Search player...          ✓]   │
├─────────────────────────────────┤
│ Registered (4/8)                │
│ 1. John Doe         [Remove]    │
│ 2. Jane Smith       [Remove]    │
│ 3. Bob Johnson      [Remove]    │
│ 4. Tom Wilson       [Remove]    │
│                                 │
│ Available Players               │
│ □ Alice Cooper     [Add]        │
│ □ Charlie Brown    [Add]        │
│ □ Diana Prince     [Add]        │
│ □ Eric Davis       [Add]        │
│ □ Frank Miller     [Add]        │
│                                 │
│          [Close] [Generate]     │
└─────────────────────────────────┘
```

**Features**:

- Search/filter available players
- Show registered player count / max
- Display seed order (sortable to reorder seeds)
- Add/remove buttons
- Visual feedback on remove (confirm dialog)
- Generate button only appears when tournament is full

---

## 3. MATCH VIEWS

### Match Details & Scoring (`/matches/{id}`)

**Purpose**: View and record match results

**Admin View**:

```
┌──────────────────────────────────────────┐
│ MATCH #1 - Round 1                       │
├──────────────────────────────────────────┤
│                                          │
│  JOHN DOE              5                 │
│  [John Doe Pic]        ◯ ◯ ◯ ◯ ◯         │
│  Ranking: 1200 pts     Wins: 5           │
│                                          │
│              vs                          │
│  [Table 3] [Referee: Bob Smith]         │
│  [Scheduled: Feb 15, 10:00]              │
│                                          │
│  JANE SMITH            3                 │
│  [Jane Pic]            ◯ ◯ ◯             │
│  Ranking: 1180 pts     Wins: 4           │
│                                          │
├──────────────────────────────────────────┤
│ STATUS: COMPLETED ✓                      │
│                                          │
│ [Edit Result] [Override] [Schedule]      │
│                                          │
│ LIVE MODE:                               │
│ [View Live] (Full screen, auto-refresh)  │
└──────────────────────────────────────────┘
```

**Result Entry Form**:

```
┌──────────────────────────────────────────┐
│ RECORD MATCH RESULT                      │
├──────────────────────────────────────────┤
│ Match: Round 1, Match #1                 │
│ Players: John Doe vs Jane Smith          │
│                                          │
│ John Doe Score  *                        │
│ [5             ]                         │
│                                          │
│ Jane Smith Score *                       │
│ [3             ]                         │
│                                          │
│ (Scores must be different - no ties)     │
│                                          │
│            [Cancel] [Record Result]      │
└──────────────────────────────────────────┘
```

**Features**:

- Large, clear display of player info
- Visual score input (large numbers)
- Real-time validation (scores must differ)
- Edit button triggers result form
- Override button for admin (shows checkbox "Mark as admin override")
- Schedule button opens scheduling modal
- Status indicator with timestamp
- Link to next match (if applicable)

### Match Scheduling Modal

**Purpose**: Assign table, referee, and time

**Modal**:

```
┌──────────────────────────────────────────┐
│ SCHEDULE MATCH                           │
├──────────────────────────────────────────┤
│ Match: John Doe vs Jane Smith            │
│                                          │
│ Scheduled Time *                         │
│ [2025-02-15] [14:30]                     │
│                                          │
│ Table Number                             │
│ [3        ]                              │
│                                          │
│ Referee                                  │
│ [Select Referee    ▼]                    │
│ - Bob Smith                              │
│ - Alice Cooper                           │
│ - (None)                                 │
│                                          │
│ Notes (optional)                         │
│ [Winner plays at 3pm table 1    ]        │
│                                          │
│         [Cancel] [Schedule]              │
└──────────────────────────────────────────┘
```

**Features**:

- Date/time picker
- Table number input (1-99)
- Referee dropdown
- Optional notes field
- Confirmation before saving

### Live Match Mode (`/matches/{id}/live`)

**Purpose**: Full-screen TV display for pool hall

**Layout**:

```
╔════════════════════════════════════════════════╗
║                   LIVE MATCH                   ║
╠════════════════════════════════════════════════╣
║                                                ║
║         JOHN DOE          JANE SMITH          ║
║                                                ║
║              5              :              3    ║
║                                                ║
║     Table 3   |   Race to 5   |   Ref: Bob    ║
║                                                ║
║  Status: IN PROGRESS                          ║
║  Last updated: 2 seconds ago                   ║
║  (Auto-refreshes every 3 seconds)              ║
║                                                ║
║  [← Back to Bracket]                           ║
║                                                ║
╚════════════════════════════════════════════════╝
```

**Features**:

- Large, easy-to-read scoreboard
- Player names and photos (when available)
- Current score prominent (large font)
- Match details (table, format, frames)
- Auto-refresh every 3 seconds
- Landscape orientation optimized
- Back button (small, bottom left)
- (Future: animated score updates, buzzer sounds)

---

## 4. ADMIN DASHBOARD

### Tournament Administration (`/admin/dashboard`)

**Purpose**: Centralized admin controls

**Layout**:

```
┌──────────────────────────────────────────┐
│ ADMIN DASHBOARD                          │
├──────────────────────────────────────────┤
│ ACTIVE TOURNAMENTS (2)                   │
│ ┌────────────────────────────────────┐   │
│ │ Regional Qualifier                 │   │
│ │ 8 players | 3 rounds | 5 complete  │   │
│ │ [View] [Edit] [Manage Players]     │   │
│ └────────────────────────────────────┘   │
│                                          │
│ MATCHES IN PROGRESS (3)                  │
│ ┌────────────────────────────────────┐   │
│ │ John vs Jane [Round 1] [5-3] [Go]  │   │
│ │ Bob vs Tom   [Round 1] [4-2] [Go]  │   │
│ │ Alice vs Ed  [Round 2] [2-2] [Go]  │   │
│ └────────────────────────────────────┘   │
│                                          │
│ PENDING MATCHES (5)                      │
│ ┌────────────────────────────────────┐   │
│ │ Winners R1 vs Winners R2 [Round 2] │   │
│ │ [Start] [Schedule] [Override]      │   │
│ └────────────────────────────────────┘   │
│                                          │
│ ACTIONS                                  │
│ [New Tournament] [Players] [Settings]    │
└──────────────────────────────────────────┘
```

---

## 5. PLAYER PROFILES

### Player Profile Page (`/players/{id}`)

**Purpose**: View player statistics and history

**Layout**:

```
┌──────────────────────────────────────────┐
│         JOHN DOE - Player Profile         │
├──────────────────────────────────────────┤
│ [Photo]                                  │
│           Nickname: "Johnny"             │
│           Ranking: 1,250 pts (#5)        │
│                                          │
│ STATISTICS                               │
│ ├─ Wins: 47                              │
│ ├─ Losses: 12                            │
│ ├─ Win Rate: 79.7%                       │
│ └─ Tournaments: 6                        │
│                                          │
│ TOURNAMENT RESULTS                       │
│ ┌────────────────────────────────────┐   │
│ │ Spring Championship   FINALIST      │   │
│ │ Regional Qualifier    CHAMPION ★    │   │
│ │ Last Month Final      4th Place     │   │
│ └────────────────────────────────────┘   │
│                                          │
│ RECENT MATCHES                           │
│ ┌────────────────────────────────────┐   │
│ │ vs Jane Smith      ✓ WIN   5-3      │   │
│ │ vs Bob Johnson     ✓ WIN   5-1      │   │
│ │ vs Tom Wilson      ✗ LOSS  2-5      │   │
│ └────────────────────────────────────┘   │
│                                          │
└──────────────────────────────────────────┘
```

---

## 6. LEADERBOARD / RANKINGS

### Global Rankings Page (`/rankings`)

**Purpose**: View all-time and monthly rankings

**Tabs**: [All-Time] [This Month] [Filter by Tournament]

**Layout**:

```
┌──────────────────────────────────────────┐
│ GLOBAL RANKINGS - ALL TIME               │
├──────────────────────────────────────────┤
│ Rank  Player            Pts   Wins  Losses
├──────────────────────────────────────────┤
│  1.   ★ John Doe       1,450   47    12   │
│  2.   ★ Jane Smith     1,380   44    15   │
│  3.   ★ Bob Johnson    1,320   42    18   │
│  ...                                     │
│ 47.   Alice Cooper       980   22    38   │
│                                          │
│ Last updated: 2 minutes ago               │
└──────────────────────────────────────────┘
```

---

## 7. COLOR & STYLING SCHEME

### Status Badge Colors

- **UPCOMING**: Blue badge
- **ONGOING**: Green badge with pulse animation
- **FINISHED**: Gray badge with checkmark
- **PENDING**: Gray
- **SCHEDULED**: Blue
- **IN_PROGRESS**: Green
- **COMPLETED**: Green with checkmark

### Player Ranking Colors

- **Top 3**: Gold, Silver, Bronze (medals)
- **Top 10**: Green tint
- **Top 25**: Neutral
- **Below 25**: Gray

### Match Result Colors

- **Win**: ✓ Green
- **Loss**: ✗ Red
- **Pending**: Gray dash
- **Scheduled**: Blue clock icon

### Typography

- **Headers**: Bold, 24-32px
- **Body**: Regular, 14-16px
- **Labels**: Semi-bold, 12-14px
- **Numbers**: Monospace font for scores

---

## 8. RESPONSIVE DESIGN

### Mobile (< 768px)

- Stack tournament info vertically
- Bracket collapses to single column
- Match cards full-width
- Standings table scrollable horizontally

### Tablet (768px - 1024px)

- Two-column layout maintained
- Bracket with reduced spacing
- Full tables visible

### Desktop (> 1024px)

- Optimal three-column layout where applicable
- Full bracket visualization
- Hover effects on interactive elements

---

## 9. USER EXPERIENCE FLOWS

### Create & Play Tournament (Happy Path)

```
1. Admin clicks "New Tournament"
2. Fills form (name, date, location, max players, bracket type)
3. Submits → Sees tournament in UPCOMING status
4. Clicks "Manage Players"
5. Searches and adds players one by one
6. Once all 8 players registered, [Generate Bracket] appears
7. Clicks "Generate Bracket" → Bracket created
8. Tournament status changes to ONGOING
9. Matches display in bracket view
10. Admin clicks match → Can record score
11. Score recorded → Winner auto-advances
12. Final match recorded → Tournament auto-finishes
13. Champion crowned, standings locked
```

### Live Match (TV Mode)

```
1. Admin navigates to match details
2. Clicks [Live Mode]
3. Full-screen scoreboard appears
4. Auto-refreshes every 3 seconds
5. Displays current scores from database
6. Admin updates score from admin panel
7. TV display automatically updates
8. (Future: QR code to view from phone, buzzer on score update)
```

### Admin Override

```
1. Match is already completed with scores
2. Admin navigates to match
3. Clicks [Override]
4. Enters new scores and selects correct winner
5. Checks "Mark as admin override"
6. Submits → Confirmation dialog
7. Old statistics reversed
8. New statistics recorded
9. Winner re-advanced to next match
10. Audit trail shows override flag
```

---

## 10. ACCESSIBILITY

✓ **Features**:

- ARIA labels on buttons
- Keyboard navigation support (Tab, Enter)
- High contrast colors for badges
- Large clickable areas (min 48x48px)
- Screen reader friendly table structure
- Form labels associated with inputs

---

## 11. NOTIFICATION MESSAGES

### Success Messages (Green)

- "Tournament created successfully"
- "Player registered"
- "Match result recorded"
- "Bracket generated"

### Error Messages (Red)

- "Players must be different (no ties)"
- "Tournament is full"
- "This player is already registered"
- "Both players must be assigned"

### Info Messages (Blue)

- "Tournament is now ONGOING"
- "3 matches pending"
- "2 matches in progress"

---

## 12. FUTURE ENHANCEMENTS

- [ ] Dark mode
- [ ] Animated bracket transitions
- [ ] WebSocket live updates (real-time without polling)
- [ ] Player avatars/photos integration
- [ ] Mobile app
- [ ] PDF bracket export
- [ ] Email notifications
- [ ] Social media integration
- [ ] Spectator QR codes for live viewing

---

**UI/UX Version**: 1.0
**Last Updated**: January 18, 2026
**Status**: Ready for Blade template implementation
