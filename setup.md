# Hiremee Setup Guide

This project uses Laravel 12, MySQL, database-backed sessions/cache/queue, Vite for frontend assets, and JWT for API authentication.

## 1. Prerequisites

Make sure these tools are installed before you start:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL
- PHP `pdo_mysql` extension enabled

## 2. Create the MySQL Database

Start MySQL, then create a database for the project.

Example:

```sql
CREATE DATABASE hireme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

If you want to use a different database name, update the `.env` values in the next step to match it.

## 3. Create and Configure `.env`

If `.env` does not exist yet, copy the example file:

```powershell
Copy-Item .env.example .env
```

Then update the database settings in `.env`.

Recommended local example:

```env
APP_NAME=Hiremee
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hireme
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

JWT_SECRET=
```

Notes:

- Use your real MySQL username and password.
- If your MySQL server uses a different host or port, change `DB_HOST` and `DB_PORT`.
- `JWT_SECRET` should stay empty for now. It will be generated with an artisan command.
- For local development, you can keep `MAIL_MAILER=log` unless you want to connect a real mail service.

## 4. Install Dependencies

Install PHP and frontend packages:

```powershell
composer install
npm install
```

## 5. Generate App Keys and Clear Cached Config

Run these commands after the `.env` file is ready:

```powershell
php artisan optimize:clear
php artisan key:generate
php artisan jwt:secret
```

What these do:

- `optimize:clear` removes stale cached config/routes/views.
- `key:generate` creates the Laravel `APP_KEY`.
- `jwt:secret` writes the JWT secret into `.env`.

## 6. Run Migrations

Because this project uses database-backed sessions, cache, queue, and auth-related tables, the migrations must be run before the app is used.

```powershell
php artisan migrate
```

If you want sample data as well, run:

```powershell
php artisan db:seed
```

## 7. Start the Application

### Option A: Minimal local run

Use separate terminals.

Terminal 1:

```powershell
php artisan serve
```

Terminal 2:

```powershell
npm run dev
```

Optional Terminal 3, if you need queued jobs to be processed:

```powershell
php artisan queue:listen --tries=1 --timeout=0
```

Then open:

```text
http://127.0.0.1:8000
```

### Option B: Full development stack with one command

This repository already defines a Composer development script that starts the backend server, queue listener, Laravel logs, and Vite together.

```powershell
composer run dev
```

That command runs:

- `php artisan serve`
- `php artisan queue:listen --tries=1 --timeout=0`
- `php artisan pail --timeout=0`
- `npm run dev`

## 8. Optional Shortcut

After the database exists and `.env` has the correct MySQL values, you can also use the built-in setup script:

```powershell
composer run setup
php artisan jwt:secret
```

Use the manual steps above if you want the most explicit setup flow.

## 9. Quick Command Summary

```powershell
Copy-Item .env.example .env
composer install
npm install
php artisan optimize:clear
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

In another terminal:

```powershell
npm run dev
```