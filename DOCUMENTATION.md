# Equipment Borrowing & Tracking System

> A Laravel 12 inventory management application designed for organizations to track equipment lifecycle, borrowing transactions, and maintenance status.

## 🧭 At a Glance

- **What it is:** A structured CRUD-based inventory management system.
- **What problem it solves:** Eliminates manual tracking of equipment loans, preventing loss and ensuring accountability.
- **Who uses it:** Custodians, IT staff, and administrators in schools or small offices.
- **Complexity level:** Intermediate.
- **Best way to explore:** Run `php artisan migrate:fresh --seed` and inspect `app/Http/Controllers/BorrowingController.php` to see how business logic flows.

## 💡 Why This Exists

Managing physical assets manually often leads to "lost" equipment and unclear ownership. This system provides a centralized source of truth for who has what, when it is due, and its current condition.

The project leverages the Laravel 12 ecosystem to enforce strict data integrity. By using Eloquent ORM relationships, it ensures that borrowing transactions are tightly coupled with equipment availability, preventing double-booking or orphaned records.

It serves as a canonical example of a "Monolithic CRUD" application, demonstrating how to organize business logic, authorization, and database schema in a modern PHP environment.

## ✨ Key Features

- **Role-Based Access Control:** Distinguishes between `admin` and `staff` to restrict sensitive actions like equipment deletion or report generation.
- **Automated Lifecycle Tracking:** Automatically updates equipment status (e.g., "Available" to "Borrowed") during transaction processing.
- **Overdue Alerts:** Centralized logic to identify and flag equipment that has passed its return date.
- **CSV Reporting:** Administrative tools to export transaction history for audit purposes.
- **Search & Filter:** Dynamic inventory lookups by category, status, or condition.

## 🏗️ Core Architecture

- **System Design Pattern**: Model-View-Controller (MVC) (Separates data, presentation, and orchestration).
- **Data Flow**: User Request → `routes/web.php` → `BorrowingController` → `BorrowingTransaction` Model → Database → View.
- **Key Abstractions**: `Equipment` (the asset), `BorrowingTransaction` (the event), and `RoleMiddleware` (the gatekeeper).
- **Boundaries & Seams**: Uses `Laravel Fortify` for authentication and `storage/` for local file system asset management.

## 🛠️ Tech Stack

- **Languages & Frameworks:** PHP 8.2+, Laravel 12.
- **Build & Tooling:** Vite 7.0, Tailwind CSS 4.0, Laravel Pint (linting), PHPUnit 11.
- **Infrastructure:** SQLite (default) or MySQL.
- **External Runtime Requirements:** PHP 8.2+, Composer, Node.js/Yarn.

## 📦 Critical Dependencies

- `laravel/framework` — The core engine providing ORM, routing, and middleware capabilities.
- `laravel/fortify` — Provides secure, backend-agnostic authentication features.
- `tailwindcss` — Utility-first CSS framework for rapid, responsive UI development.

## 🗂️ Project Structure

```text
/app            → Core business logic, models, controllers, and middleware
/bootstrap      → Application bootstrapping and service registration
/config         → System-wide configuration files
/database       → Migrations, seeders, and model factories
/public         → Entry point for web requests and compiled assets
/resources      → Blade templates, CSS, and JS source files
/routes         → URL definitions and endpoint mapping
/storage        → Logs, file uploads, and cached framework data
/tests          → Unit and feature test suites
```

_Mental Map: To understand this project, think of it as a state machine where the "Equipment" entity changes state based on "BorrowingTransaction" events._

## 🔍 Where to Start Reading

**For engineers:**

- `app/Models/Equipment.php` — _Defines the core schema and relationships for assets._
- `app/Http/Controllers/BorrowingController.php` — _The primary orchestration layer for business logic._
- `app/Models/BorrowingTransaction.php` — _The central entity tracking the lifecycle of a loan._

**For learners:**

- `app/Models/Equipment.php` — _Excellent example of how to define database relationships in Laravel._
- `README.md` — _Provides the high-level context and setup instructions._
- `routes/web.php` — _The best place to see how URLs map to specific code actions._

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & Yarn
- MySQL (or SQLite)

### Setup

```bash
composer install          # Install PHP dependencies
yarn install              # Install frontend dependencies
cp .env.example .env      # Configure environment variables
php artisan key:generate  # Set application encryption key
php artisan migrate:fresh --seed # Build database and add demo data
php artisan serve         # Start the local development server
```

### Verify It's Working

Navigate to `http://127.0.0.1:8000`. You should see the welcome page; log in with `admin@example.com` / `password` to access the dashboard.

## 🤝 How to Contribute

**Jump right in:**

- [Open in GitHub Codespaces](https://codespaces.new/snaveee/equipment-system)

**Contribution path for first-timers:**

1. Improve documentation in `README.md`.
2. Add unit tests for `Equipment` model methods in `tests/`.
3. A good PR includes a clear description of the business logic change and corresponding test coverage.

**Testing & linting before you push:**

```bash
./vendor/bin/pint    # Run code style fixer
php artisan test     # Run the test suite
```

## 🐛 Active Good First Issues

> No issues labeled **good first issue**. Check [Issues](https://github.com/snaveee/equipment-system/issues) for `help wanted`, `docs`, or `bug`.

## 📚 What You'll Learn

- **Eloquent Relationships:** Mastering `hasMany` and `belongsTo` for complex data.
- **Middleware Orchestration:** How to protect routes based on user roles.
- **Lifecycle Management:** Implementing state transitions in a database-driven app.
- **Modern Laravel 12 Patterns:** Using the latest framework features for clean, maintainable code.

## 🤖 Machine-Readable Metadata [AI-READABLE]

```yaml
repo: snaveee/equipment-system
description: "Laravel 12 equipment inventory and borrowing system"
stars: 0
forks: 0
open_issues: 0
language: "PHP"
license: "none"
architecture_pattern: "MVC"
entry_point: "app/Http/Controllers/BorrowingController.php"
external_dependencies_required: true
test_command: "php artisan test"
ci_present: false
```
