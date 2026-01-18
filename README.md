# 🎱 Theatro Pool - Tournament Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

A professional Laravel web application for managing 8-Ball Pool tournaments in billiard halls. Features a beautiful billiard-inspired dark/light theme with real-time bracket visualization.

---

## 📋 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Routes](#-routes)
- [Technical Architecture](#-technical-architecture)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 🏆 Core Features

| Feature                    | Description                                                                      |
| -------------------------- | -------------------------------------------------------------------------------- |
| **Authentication & Roles** | Admin and Player roles with secure Laravel authentication                        |
| **Player Management**      | Create, edit, delete players with automatic win/loss tracking                    |
| **Tournament Management**  | Create tournaments (8, 16, or 32 players) with single-elimination bracket format |
| **Bracket Visualization**  | Real-time interactive bracket display                                            |
| **Match Management**       | Update results with automatic winner advancement                                 |
| **Dashboard**              | Admin dashboard with stats, active tournaments, and recent matches               |

### 👤 Player Features

- **User Profiles**: Each player has their own profile they can view and edit
- **Self-Registration**: Players can join tournaments themselves
- **Tournament Management**: Players can leave tournaments before they start
- **Auto Player Creation**: New users automatically get a player profile

### 📊 Analytics & Insights

- **Rankings**: Player leaderboard with sorting by win rate, wins, and matches
- **Statistics**: Comprehensive analytics including top scorers, championships, format popularity
- **Head-to-Head**: Compare any two players' performance against each other
- **Activity Feed**: Real-time feed of recent matches and tournament updates

### 🎨 Design Features

- **Dark/Light Mode**: Toggle between themes with a beautiful billiard-inspired color palette
- **Mobile Responsive**: Full mobile support with hamburger menu navigation
- **Print Support**: Print-friendly tournament brackets
- **Accessibility**: Reduced motion support for users with vestibular disorders

---

## 🖼️ Screenshots

> _Add screenshots of your application here_

---

## 📦 Requirements

| Requirement   | Version                          |
| ------------- | -------------------------------- |
| PHP           | 8.2 or higher                    |
| Composer      | Latest                           |
| Database      | MySQL 5.7+ or SQLite             |
| Node.js & NPM | Optional (for asset compilation) |

---

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/JihadMouabbad/THEATRO-POOL-01.git
cd THEATRO-POOL-01
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

#### Option A: SQLite (Recommended for Development)

```bash
# Create SQLite database file
# On Windows (PowerShell):
New-Item -Path "database/database.sqlite" -ItemType File

# On Linux/Mac:
touch database/database.sqlite
```

Update your `.env` file:

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Option B: MySQL (Recommended for Production)

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=theatro_pool
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then create the database:

```sql
CREATE DATABASE theatro_pool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

### Step 6: Seed Demo Data (Optional but Recommended)

```bash
php artisan db:seed
```

This creates:
| Data | Details |
|------|---------|
| Admin User | `admin@theatropool.com` / `password` |
| Player User | `player@theatropool.com` / `password` |
| Demo Players | 16 players with varied stats |
| Tournaments | 4 tournaments (upcoming, ongoing, finished) |

### Step 7: Start the Development Server

```bash
php artisan serve
```

🎉 **Visit** `http://localhost:8000` **in your browser!**

---

## ⚡ Quick Start

For a rapid setup, run these commands in sequence:

```bash
# Clone repository
git clone https://github.com/JihadMouabbad/THEATRO-POOL-01.git
cd THEATRO-POOL-01

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create SQLite database (Windows PowerShell)
New-Item -Path "database/database.sqlite" -ItemType File

# Run migrations and seed
php artisan migrate --seed

# Start server
php artisan serve
```

**Demo Credentials:**

- 🔐 **Admin**: `admin@theatropool.com` / `password`
- 👤 **Player**: `player@theatropool.com` / `password`

---

## ⚙️ Configuration

### Environment Variables

| Variable        | Description                    | Default      |
| --------------- | ------------------------------ | ------------ |
| `APP_NAME`      | Application name               | Theatro Pool |
| `APP_ENV`       | Environment (local/production) | local        |
| `APP_DEBUG`     | Debug mode                     | true         |
| `DB_CONNECTION` | Database driver                | sqlite       |
| `MAIL_MAILER`   | Mail driver                    | log          |

### Cache Configuration

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📖 Usage

### 👨‍💼 Admin Functions

1. **Manage Players**: Add, edit, or delete players from the Players page
2. **Create Tournaments**: Set up new tournaments with 8, 16, or 32 player formats
3. **Register Players**: Add any players to upcoming tournaments
4. **Generate Brackets**: Start the tournament when all players are registered
5. **Enter Match Results**: Update scores; winners automatically advance to next round

### 👤 Player Functions

1. **View Tournaments**: Browse all tournaments and see live brackets
2. **Join Tournaments**: Register for upcoming tournaments with available spots
3. **Leave Tournaments**: Withdraw from tournaments before they start
4. **View Profile**: See your own stats, matches, and registered tournaments
5. **Edit Profile**: Update your nickname and contact information
6. **Compare Stats**: Use Head-to-Head to compare with other players

---

## 🗺️ Routes

| Page              | Route               | Description                        | Auth |
| ----------------- | ------------------- | ---------------------------------- | ---- |
| Home              | `/`                 | Landing page with live tournaments | ❌   |
| Tournaments       | `/tournaments`      | List all tournaments               | ❌   |
| Tournament Detail | `/tournaments/{id}` | View bracket, join tournament      | ❌   |
| Players           | `/players`          | List all players                   | ❌   |
| Player Profile    | `/players/{id}`     | Player stats and history           | ❌   |
| Rankings          | `/rankings`         | Player leaderboard                 | ❌   |
| Head-to-Head      | `/head-to-head`     | Compare two players                | ❌   |
| Activity          | `/activity`         | Recent matches feed                | ❌   |
| Statistics        | `/statistics`       | Analytics dashboard                | ❌   |
| Rules             | `/rules`            | 8-Ball Pool rules                  | ❌   |
| Archive           | `/archive`          | Finished tournaments               | ❌   |
| Login             | `/login`            | User authentication                | ❌   |
| Register          | `/register`         | New user registration              | ❌   |
| Dashboard         | `/dashboard`        | Admin control panel                | ✅   |
| My Profile        | `/profile`          | User's own profile                 | ✅   |

---

## 🏗️ Technical Architecture

### Database Schema

```
┌─────────────┐     ┌─────────────────────┐     ┌─────────────┐
│   users     │────▶│  tournament_player  │◀────│  players    │
└─────────────┘     └─────────────────────┘     └─────────────┘
                              │                        │
                              ▼                        │
                    ┌─────────────────┐               │
                    │   tournaments   │               │
                    └─────────────────┘               │
                              │                        │
                              ▼                        │
                    ┌─────────────────┐               │
                    │    matches      │◀──────────────┘
                    └─────────────────┘
```

| Table               | Purpose                                |
| ------------------- | -------------------------------------- |
| `users`             | Authentication with admin/player roles |
| `players`           | Pool players with statistics           |
| `tournaments`       | Tournament details and status          |
| `tournament_player` | Player registration pivot table        |
| `matches`           | Individual matches with scores         |

### Key Components

| Component          | Purpose                                                |
| ------------------ | ------------------------------------------------------ |
| `BracketGenerator` | Handles bracket generation and match result processing |
| `AdminMiddleware`  | Protects admin-only routes                             |
| `View Composer`    | Provides footer statistics across all pages            |

### Controllers

| Controller             | Purpose                     |
| ---------------------- | --------------------------- |
| `HomeController`       | Landing page with stats     |
| `TournamentController` | Tournament CRUD, join/leave |
| `PlayerController`     | Player CRUD and profiles    |
| `MatchController`      | Match result processing     |
| `ProfileController`    | User profile management     |
| `RankingsController`   | Player leaderboard          |
| `StatisticsController` | Analytics dashboard         |
| `HeadToHeadController` | Player comparison           |
| `ActivityController`   | Recent activity feed        |
| `RulesController`      | Rules documentation         |
| `ArchiveController`    | Finished tournaments        |
| `DashboardController`  | Admin dashboard             |

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👤 Author

**Jihad Mouabbad**

- GitHub: [@JihadMouabbad](https://github.com/JihadMouabbad)

---

<p align="center">
  Made with ❤️ for billiard enthusiasts
</p>
