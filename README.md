# 🎱 Theatro Pool - Tournament Management System

A production-ready Laravel web application for managing 8-Ball Pool tournaments in billiard halls.

## Features

### Core Features
- **Authentication & Roles**: Admin and Player roles with secure Laravel authentication
- **Player Management**: Create, edit, delete players with automatic win/loss tracking
- **Tournament Management**: 
  - Create tournaments (8, 16, or 32 players)
  - Single-elimination bracket format
  - Automatic bracket generation
  - Real-time bracket visualization
- **Match Management**: Update results with automatic winner advancement
- **Dashboard**: Admin dashboard with stats, active tournaments, and recent matches

### Player Features
- **User Profiles**: Each player has their own profile they can view and edit
- **Self-Registration**: Players can join tournaments themselves
- **Tournament Management**: Players can leave tournaments before they start
- **Auto Player Creation**: New users automatically get a player profile

### Analytics & Insights
- **Rankings**: Player leaderboard with sorting by win rate, wins, and matches
- **Statistics**: Comprehensive analytics including top scorers, championships, format popularity
- **Head-to-Head**: Compare any two players' performance against each other
- **Activity Feed**: Real-time feed of recent matches and tournament updates

### Additional Features
- **Rules Page**: Complete 8-Ball Pool rules and tournament format documentation
- **Archive**: Browse finished tournaments with search and year filtering
- **Mobile Responsive**: Full mobile support with hamburger menu navigation
- **Print Support**: Print-friendly tournament brackets
- **Accessibility**: Reduced motion support for users with vestibular disorders

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or SQLite (for development)
- Node.js & NPM (optional, for asset compilation)

## Quick Start

```bash
# Clone and setup
git clone https://github.com/JihadMouabbad/THEATRO-POOL.git
cd THEATRO-POOL

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database (SQLite for development)
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

**Demo Credentials:**
- **Admin**: `admin@theatropool.com` / `password`
- **Player**: `player@theatropool.com` / `password`

## Installation (Detailed)

### 1. Clone the repository

```bash
git clone https://github.com/JihadMouabbad/THEATRO-POOL.git
cd THEATRO-POOL
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database

For **SQLite** (development):
```bash
touch database/database.sqlite
```

For **MySQL** (production), update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=theatro_pool
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Seed demo data (optional)

```bash
php artisan db:seed
```

This creates:
- **Admin user**: `admin@theatropool.com` / `password`
- **Player user**: `player@theatropool.com` / `password`
- 16 demo players
- 4 tournaments in various states (upcoming, ongoing, finished)

### 7. Start the development server

```bash
php artisan serve
```

## All Pages / Routes

| Page | Route | Description |
|------|-------|-------------|
| Home | `/` | Landing page with live tournaments, upcoming events, Hall of Fame |
| Tournaments | `/tournaments` | List all tournaments with filtering |
| Tournament Detail | `/tournaments/{id}` | View bracket, join tournament, enter results |
| Players | `/players` | List all players with search |
| Player Profile | `/players/{id}` | Player stats, match history, tournament participation |
| Rankings | `/rankings` | Player leaderboard with sorting options |
| Head-to-Head | `/head-to-head` | Compare two players' performance |
| Activity | `/activity` | Recent matches and tournament updates feed |
| Statistics | `/statistics` | Comprehensive analytics dashboard |
| Rules | `/rules` | 8-Ball Pool rules and tournament format |
| Archive | `/archive` | Finished tournaments with search and filtering |
| Login | `/login` | User authentication |
| Register | `/register` | New user registration |
| Dashboard | `/dashboard` | Admin control panel (auth required) |
| My Profile | `/profile` | User's own profile (auth required) |

## Usage

### Admin Functions

1. **Manage Players**: Add, edit, or delete players from the Players page
2. **Create Tournaments**: Set up new tournaments with 8, 16, or 32 player formats
3. **Register Players**: Add any players to upcoming tournaments
4. **Generate Brackets**: Start the tournament when all players are registered
5. **Enter Match Results**: Update scores; winners automatically advance to next round

### Player Functions

1. **View Tournaments**: Browse all tournaments and see live brackets
2. **Join Tournaments**: Register for upcoming tournaments with available spots
3. **Leave Tournaments**: Withdraw from tournaments before they start
4. **View Profile**: See your own stats, matches, and registered tournaments
5. **Edit Profile**: Update your nickname and contact information
6. **Compare Stats**: Use Head-to-Head to compare with other players

## Technical Architecture

### Database Schema

- `users` - Authentication with admin/player roles, linked to player profiles
- `players` - Pool players with statistics (wins, losses, total_matches)
- `tournaments` - Tournament details and status (upcoming/ongoing/finished)
- `tournament_player` - Player registration pivot table with seeding
- `matches` - Individual matches with scores and bracket linking

### Key Components

- **BracketGenerator Service**: Handles bracket generation and match result processing
- **AdminMiddleware**: Protects admin-only routes
- **View Composer**: Provides footer statistics across all pages
- **Eloquent Models**: Full relationship definitions for all entities

### Controllers

| Controller | Purpose |
|------------|---------|
| HomeController | Landing page with stats |
| TournamentController | Tournament CRUD, join/leave |
| PlayerController | Player CRUD and profiles |
| MatchController | Match result processing |
| ProfileController | User profile management |
| RankingsController | Player leaderboard |
| StatisticsController | Analytics dashboard |
| HeadToHeadController | Player comparison |
| ActivityController | Recent activity feed |
| RulesController | Rules documentation |
| ArchiveController | Finished tournaments |
| DashboardController | Admin dashboard |

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
