# NotesTips

Private notes web application built with Laravel. It allows users to sign up, log in, create/edit/delete notes, and organize them with search, filters, and sorting.

Demo: https://notestips.jmsweb.site

## Features

- Full authentication flow: register, login, logout, and `remember me`.
- Brute-force protection on login using `RateLimiter` (temporary lock after multiple failed attempts).
- Per-user notes CRUD (ownership isolation through policy authorization).
- Optional `importance` metadata with values `baja`, `media`, and `alta`.
- Optional `due_date` metadata for scheduling.
- Title/content search with combinable filters (importance, date, sorting).
- Safe Markdown rendering (unsafe HTML disabled).
- Interactive checklist support: toggle tasks (`- [ ]`, `- [x]`) directly from the note detail view.
- Responsive UI built with Tailwind CSS + Alpine.js.

## Technical Stack

- PHP 8.2
- Laravel 12
- MySQL 8
- Blade + Tailwind CSS 4 + Alpine.js
- Vite
- Pest (test suite)

## Architecture Overview

- `routes/web.php`: web routes for home, authentication, and notes.
- `app/Http/Controllers/NotesController.php`: core notes logic, search, and task toggling.
- `app/Policies/NotePolicy.php`: ownership-based authorization.
- `app/Services/RegisteredUserService.php`: user creation and welcome note bootstrap.
- `resources/views/`: Blade views (landing, auth, notes dashboard).
- `resources/js/pages/list.js`: dynamic filters and list refresh via `/notes/search`.

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18 and npm
- MySQL 8 (local or via Docker)

## Installation

### 1) Clone repository

```bash
git clone https://github.com/joel12-Sant/notestips.git
cd notestips
```

### 2) Configure environment

```bash
cp .env.example .env
```

### 3) Install dependencies

```bash
composer install
npm install
```

### 4) Database

Optional with Docker (includes MySQL and phpMyAdmin):

```bash
docker compose up -d
```

Default `.env.example` database settings:

- `DB_HOST=127.0.0.1`
- `DB_PORT=3316`
- `DB_DATABASE=gestor_de_notas`
- `DB_USERNAME=root`
- `DB_PASSWORD=root`

### 5) Initialize the application

```bash
php artisan key:generate
php artisan migrate
```

### 6) Start development environment

In two terminals:

```bash
php artisan serve
npm run dev
```

Open: http://127.0.0.1:8000

## Quick Setup Script

You can also use the Composer setup script:

```bash
composer run setup
```

This command installs dependencies, prepares `.env`, generates `APP_KEY`, runs migrations, and builds assets.

## Usage Flow

1. Create an account (`/auth/register`).
2. Log in (`/auth/login`).
3. Create notes at `/notes/create`.
4. Filter and search from the notes dashboard.
5. Open a note to view rendered Markdown and toggle tasks.

## Testing

Run the test suite:

```bash
composer test
```

Current coverage includes authentication flows, ownership/authorization checks, notes CRUD, search/filter behavior, and task-toggle edge cases.
