# 🎱 Theatro Pool - Tournament Management System

A production-ready Laravel web application for managing 8-Ball Pool tournaments in billiard halls.

## Features

- **Authentication & Roles**: Admin and Player roles with secure Laravel authentication
- **Player Management**: Create, edit, delete players with win/loss tracking
- **Tournament Management**: 
  - Create tournaments (8, 16, or 32 players)
  - Single-elimination bracket format
  - Automatic bracket generation
  - Real-time bracket visualization
- **Match Management**: Update results with automatic winner advancement
- **Dashboard**: Admin dashboard with stats, active tournaments, and recent matches

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or SQLite (for development)
- Node.js & NPM (optional, for asset compilation)

## Installation

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

Visit `http://localhost:8000` in your browser.

## Usage

### Admin Functions

1. **Manage Players**: Add, edit, or delete players
2. **Create Tournaments**: Set up new tournaments with 8, 16, or 32 player formats
3. **Register Players**: Add players to tournaments
4. **Generate Brackets**: Start the tournament when all players are registered
5. **Enter Match Results**: Update scores; winners automatically advance

### Player Functions

1. View tournaments and brackets
2. View player profiles and statistics
3. Track match history

## Technical Architecture

### Database Schema

- `users` - Authentication with admin/player roles
- `players` - Pool players with statistics
- `tournaments` - Tournament details and status
- `tournament_player` - Player registration pivot table
- `matches` - Individual matches with scores

### Key Components

- **BracketGenerator Service**: Handles bracket generation and match result processing
- **AdminMiddleware**: Protects admin-only routes
- **Eloquent Models**: Full relationship definitions for all entities

### Routes

- `/` - Home page with live tournaments, upcoming events, and Hall of Fame
- `/login`, `/register` - Authentication
- `/dashboard` - Admin dashboard with stats and management links
- `/players` - Player listing and CRUD
- `/players/{id}` - Player profile with stats and match history
- `/tournaments` - Tournament listing and management
- `/tournaments/{id}` - Tournament detail with bracket visualization
- `/rankings` - Player leaderboard with sorting options
- `/statistics` - Comprehensive analytics and stats
- `/archive` - Finished tournaments archive with search
- `/matches/{id}` - Match details and result entry

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
