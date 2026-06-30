# Fixed Asset Management System

> A web-based system for tracking, managing, and auditing fixed IT and non-IT assets across BFC Group farm locations.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink?logo=livewire)
![License](https://img.shields.io/badge/License-MIT-green)

---

## Table of Contents

- [About](#about)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Environment Variables](#environment-variables)
- [Running Locally](#running-locally)
- [Testing](#testing)
- [Folder Structure](#folder-structure)
- [Deployment](#deployment)

---

## About

The Fixed Asset Management System (FAMS) is built for BFC Group to track and manage all fixed assets — IT equipment, machinery, and other physical assets — across multiple farm locations (BFC, BDL, PFC, RH, BBGC, Hatchery).

It covers the full asset lifecycle from acquisition through transfer and disposal, with role-based approval workflows, audit trails, and AI-powered analytics.

**Key features:**

- Multi-farm asset registry with full lifecycle tracking (acquisition → issuance → transfer → disposal)
- Role-based access control with granular per-module permissions
- Disposal and transfer approval workflows (Farm → Division Head → VP → Accounting)
- AI-powered analytics and insights via OpenRouter API with 6-month trend tracking
- QR code generation and affixing status management
- Excel import/export for assets, audit logs, and repair logs
- In-app notification system for workflow events
- Lost asset investigation workflow (investigate → found or write off)
- SME periodic review workflow

---

## Tech Stack

| Layer        | Technology                              |
|--------------|-----------------------------------------|
| Framework    | Laravel 12.x                            |
| Language     | PHP 8.2+                                |
| Database     | MySQL 8.0                               |
| Realtime UI  | Livewire 3.x                            |
| JS           | Alpine.js (bundled with Livewire)       |
| CSS          | Tailwind CSS 4.x                        |
| Build        | Vite 7.x                                |
| Queue        | Redis (predis)                          |
| QR Codes     | simplesoftwareio/simple-qrcode          |
| Excel        | maatwebsite/excel 3.x                   |
| AI           | OpenRouter API (llama-3.3-70b default)  |
| Icons        | Font Awesome 7                          |

---

## Prerequisites

- **PHP** >= 8.2 with extensions: `mbstring`, `xml`, `pdo`, `curl`, `zip`
- **Composer** >= 2.x
- **Node.js** >= 20.x and **npm** >= 10.x
- **MySQL** >= 8.0
- **Redis** >= 7.x (for queues)

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/FixedAssetSystem.git
cd FixedAssetSystem

# 2. Run the full setup in one command
composer setup
```

The `composer setup` script handles: `composer install`, `.env` copy, `key:generate`, `migrate`, `npm install`, and `npm run build`.

Or step by step:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed    # optional — seeds sample data
npm install
npm run build
php artisan storage:link
```

---

## Environment Variables

Copy `.env.example` to `.env` and update the values below.

```env
# Application
APP_NAME=FAMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Queue & Cache
QUEUE_CONNECTION=redis
CACHE_STORE=file
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# AI Analytics (optional — Analytics module requires this)
OPENROUTER_API_KEY=your_openrouter_key
OPENROUTER_MODEL=meta-llama/llama-3.3-70b-instruct   # default

# Snipe-IT Integration (optional)
SNIPE_URL=http://your-snipe-host/api/v1
SNIPE_TOKEN=your_snipe_api_token
```

> **Note:** Never commit your `.env` file. It is already listed in `.gitignore`.

---

## Running Locally

The easiest way — runs the dev server, queue worker, and Vite all at once:

```bash
composer dev
```

Or start each separately in its own terminal:

```bash
php artisan serve          # Laravel dev server
php artisan queue:listen   # Queue worker (notifications, exports)
npm run dev                # Vite hot reload
```

**Using Laragon:** Simply place the project in `C:\laragon\www\` and Laragon handles the web server and MySQL automatically.

---

## Testing

The project uses PHPUnit for testing.

```bash
# Run all tests
php artisan test

# Run with coverage report
php artisan test --coverage

# Run a specific test file
php artisan test --filter ExampleTest
```

---

## Folder Structure

```
app/
├── Exports/          # Excel export classes (assets, audit log, repair log)
├── Http/
│   └── Controllers/  # Route controllers
├── Livewire/         # 21+ Livewire full-page and inline components
├── Models/           # Eloquent models (Asset, Employee, History, ...)
├── Services/         # NotificationService and other business logic
└── Support/          # AccessControl helper

database/
├── migrations/       # All schema migrations
└── seeders/          # DatabaseSeeder + role/permission seeders

resources/
├── css/              # global.css + Tailwind entry
├── js/               # global.js (sidebar, nav)
└── views/
    ├── layouts/      # app.blade.php (nav, header, notification toast)
    └── livewire/     # Blade views for each Livewire component

public/
├── img/              # App logo, category icons, favicon
└── js/ css/          # Built assets (Vite output)
```

---

## Deployment

**Environment:** Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
php artisan migrate --force
php artisan storage:link
```

**Cron entry** (add to server crontab):

```
* * * * * cd /var/www/FixedAssetSystem && php artisan schedule:run >> /dev/null 2>&1
```

**Supervisor config** for queue workers (`/etc/supervisor/conf.d/fams-worker.conf`):

```ini
[program:fams-worker]
command=php /var/www/FixedAssetSystem/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2
```
