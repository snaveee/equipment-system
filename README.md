# Equipment Borrowing & Tracking System

A Laravel 12 application for managing equipment inventory and borrowing transactions in schools, offices, and organizations.

**Stack:** Laravel 12 · Laravel Fortify (auth) · Tailwind CSS v4 · MySQL · Blade

---

## Common Requirements Checklist

| # | Requirement | Implementation |
|---|---|---|
| 1 | Laravel 12+ | `Laravel Framework 12.60.2` |
| 2 | Fortify auth (login / register / password reset) | `App\Providers\FortifyServiceProvider` + custom Blade views in `resources/views/auth/` |
| 3 | Strict MVC | `app/Models`, `app/Http/Controllers`, `resources/views` |
| 4 | Eloquent ORM only | No raw SQL — all queries via Eloquent / Query Builder |
| 5 | Migrations + seeders | `database/migrations/`, `database/seeders/DatabaseSeeder.php` |
| 6 | Model relationships | `hasMany`, `belongsTo`, `belongsToMany` in `Equipment`, `Borrower`, `BorrowingTransaction`, `User` |
| 7 | Form Request validation | `app/Http/Requests/` (`EquipmentRequest`, `BorrowerRequest`, `BorrowingStoreRequest`, `BorrowingReturnRequest`) |
| 8 | Master Blade layout | `resources/views/layouts/app.blade.php` with `@yield`/`@section` |
| 9 | Route grouping + `Route::resource` | `routes/web.php` uses `prefix`, `name`, `middleware`, and resource routes |
| 10 | Role-based middleware | `App\Http\Middleware\RoleMiddleware` aliased as `role:` in `bootstrap/app.php` |
| 11 | Pagination ≥ 10/page | All index pages use `->paginate(10)` |
| 12 | Flash messages | `resources/views/partials/flash.blade.php` (success / error / warning / info + validation errors) |
| 13 | Clean commented code | Documented controllers, models, requests |

## Required Modules

| Module | Where |
|---|---|
| **Equipment Management** | `EquipmentController` (resource), photo upload, search + filters by category/status/condition |
| **Borrower Management** | `BorrowerController` (resource), frequent borrower indicator, full borrow history |
| **Borrowing Transactions** | `BorrowingController` — validates availability before approving, auto-updates equipment status on borrow/return |
| **Overdue & Damage Tracking** | `borrowings.overdue` & `borrowings.damaged`, dashboard alerts, damage remarks + follow-up |
| **Reports & Statistics** | `ReportController` — most borrowed, by department, condition summary, monthly chart, **CSV export** |
| **User Authentication & Roles** | Fortify + `users.role` (`admin` / `staff`), `role:admin` middleware |

---

## Run locally

```bash
# 1. Install PHP dependencies
composer install

# 2. Install node deps + build assets
yarn install
yarn build      # or `yarn dev` for hot-reload during development

# 3. Configure environment
cp .env.example .env
php artisan key:generate
# Set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env to point to your MySQL

# 4. Run migrations + seeders
php artisan migrate:fresh --seed

# 5. Symlink public storage for equipment photos
php artisan storage:link

# 6. Serve
php artisan serve
# → http://127.0.0.1:8000
```

## Demo accounts (seeded)

| Role | Email | Password |
|---|---|---|
| Admin / Custodian | `admin@example.com` | `password` |
| Staff / Borrower | `staff@example.com` | `password` |

## Routes overview

| Method | URI | Name | Access |
|---|---|---|---|
| GET | `/` | `home` | public (welcome) |
| GET | `/login`, `/register`, `/forgot-password`, `/reset-password/{token}` | Fortify | guest |
| GET | `/dashboard` | `dashboard` | auth |
| `Route::resource` | `/equipment` | `equipment.*` | auth (writes admin-only) |
| `Route::resource` | `/borrowers` | `borrowers.*` | auth (writes admin-only) |
| GET\|POST | `/borrowings`, `/create`, `/{borrowing}` | `borrowings.*` | auth |
| GET\|POST | `/borrowings/{borrowing}/return` | `borrowings.return.*` | `role:admin` |
| GET | `/borrowings/overdue`, `/damaged` | | auth |
| GET | `/reports`, `/reports/export/transactions` | `reports.*` | `role:admin` |
