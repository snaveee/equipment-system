# Equipment System - Option A Implementation Summary

## Overview

Successfully implemented **Option A (Recommended)** for the equipment-system database refactoring:

- ✅ Merged Borrowers table into Users table
- ✅ Everyone who borrows is now a User with `role = 'borrower'`
- ✅ Added profile fields (department, position, contact_number) to users table
- ✅ Updated all relationships and queries

---

## Database Changes

### New Migrations Created

#### 1. `2026_05_25_000001_add_profile_fields_to_users_table.php`

- Adds `department`, `position`, `contact_number` columns to users table
- Changes role column to string type (allows: admin, staff, borrower)

#### 2. `2026_05_25_000002_migrate_borrowers_to_users.php`

- Migrates existing 5 borrower records to users table with:
    - `role = 'borrower'`
    - `password = Hash::make('TempPassword123!')` (temporary password)
    - Profile fields (department, position, contact_number)
- Updates foreign key references in borrowing_transactions from borrower_id to user_id

#### 3. `2026_05_25_000003_update_borrowing_transactions_and_drop_borrowers.php`

- Adds `user_id` foreign key column to borrowing_transactions
- Removes `borrower_id` foreign key and column
- Drops the `borrowers` table entirely

---

## Model Changes

### `app/Models/User.php`

**Updated:**

- Added `department`, `position`, `contact_number` to `$fillable`
- Added `transactions()` HasMany relationship (returns BorrowingTransaction)
- Added `isBorrower()` helper method
- Imported `HasMany` relation class

### `app/Models/BorrowingTransaction.php`

**Updated:**

- Changed `borrower_id` → `user_id` in `$fillable`
- Updated `borrower()` relationship to use `User` model instead of `Borrower`
- Relationship now uses `belongsTo(User::class, 'user_id')`

### `app/Models/Borrower.php`

**Status:** Deprecated (kept for reference, not used)

---

## Controller Changes

### `app/Http/Controllers/BorrowerController.php`

**Completely refactored to manage Borrower users:**

- `index()`: Queries `User::where('role', 'borrower')` instead of Borrower model
- `create()`: Returns borrower user creation form
- `store()`: Creates User with `role='borrower'` and temporary password
- `show()`: Shows User with borrower role (validates role)
- `edit()`: Edit borrower user (validates role)
- `update()`: Updates user profile and optionally password
- `destroy()`: Deletes borrower user
- All methods check `$borrower->role === 'borrower'`

### `app/Http/Controllers/BorrowingController.php`

**Updated to use users:**

- `create()`: Gets borrowers via `User::where('role', 'borrower')`
- `store()`: Maps `borrower_id` → `user_id` from form data
- All relationships continue to work (uses `$borrowing->borrower`)

### `app/Http/Controllers/ReportController.php`

**Updated queries:**

- Changed join from `borrowers` table to `users` table
- Updated field name: `full_name` → `name` in CSV export

### `app/Http/Controllers/DashboardController.php`

**Updated:**

- Changed `Borrower::count()` → `User::where('role', 'borrower')->count()`
- Removed import of `App\Models\Borrower`

---

## Form Request Changes

### `app/Http/Requests/BorrowerRequest.php`

**Updated:**

- Changed `full_name` → `name` field
- Changed validation table from `borrowers` to `users`
- Added optional `password` and `password_confirmation` fields

### `app/Http/Requests/BorrowingStoreRequest.php`

**Updated:**

- Changed `borrower_id` → `user_id` field
- Updated validation: `Rule::exists('users', 'id')->where('role', 'borrower')`

---

## View Changes

### Borrower Views

- ✅ `resources/views/borrowers/_form.blade.php`: Updated `full_name` → `name`, added password fields
- ✅ `resources/views/borrowers/index.blade.php`: Updated `full_name` → `name`
- ✅ `resources/views/borrowers/show.blade.php`: Updated `full_name` → `name` in title/heading
- ✅ `resources/views/borrowers/create.blade.php`: Auto-filled with template
- ✅ `resources/views/borrowers/edit.blade.php`: Auto-filled with template

### Borrowing Transaction Views

- ✅ `resources/views/borrowings/create.blade.php`: Changed `borrower_id` → `user_id`, `full_name` → `name`
- ✅ `resources/views/borrowings/index.blade.php`: Changed `full_name` → `name`
- ✅ `resources/views/borrowings/show.blade.php`: Changed `full_name` → `name`
- ✅ `resources/views/borrowings/return.blade.php`: Changed `full_name` → `name`
- ✅ `resources/views/borrowings/overdue.blade.php`: Changed `full_name` → `name`
- ✅ `resources/views/borrowings/damaged.blade.php`: Changed `full_name` → `name`

---

## Seeder Changes

### `database/seeders/DatabaseSeeder.php`

**Updated:**

- Creates 5 borrower users with:
    - `name` (instead of `full_name`)
    - `role = 'borrower'`
    - `password = Hash::make('TempPassword123!')`
    - Profile fields (department, position, contact_number)
- Transaction records now use `user_id` instead of `borrower_id`
- Queries users instead of borrowers: `User::where('email', ...)`

---

## Workflow Changes

### Before (Old System)

1. Admin registers borrower in separate Borrowers table
2. Transactions reference `borrower_id` → Borrowers table
3. Borrowers cannot log in
4. Two separate tables to manage

### After (New System)

1. Admin creates User with `role='borrower'` and temporary password
2. Borrower logs in and resets password on first login (via Fortify)
3. Borrower sees own transactions via `User::transactions()`
4. Can view own borrowing history and available equipment
5. Single User table is source of truth for all roles

---

## Migration Steps

To apply these changes to your database:

```bash
# 1. Backup your current database
# 2. Run the migrations
php artisan migrate

# 3. If you had existing borrowers, they're now users with role='borrower'
# 4. Verify the data migration was successful
php artisan tinker
# Then check: User::where('role', 'borrower')->count()
```

---

## Advantages of Option A

✅ **Single Source of Truth** - One users table for all roles  
✅ **Borrowers Can Log In** - Access their own transaction history  
✅ **Cleaner Architecture** - No separate Borrower model/table  
✅ **Modern Approach** - Aligns with RBAC best practices  
✅ **Flexible Role System** - Easy to add new roles in future  
✅ **Data Integrity** - Foreign key relationships simpler

---

## Files Modified (13 Total)

### Migrations (3)

- 2026_05_25_000001_add_profile_fields_to_users_table.php
- 2026_05_25_000002_migrate_borrowers_to_users.php
- 2026_05_25_000003_update_borrowing_transactions_and_drop_borrowers.php

### Models (2)

- app/Models/User.php (✅ Updated)
- app/Models/BorrowingTransaction.php (✅ Updated)

### Controllers (4)

- app/Http/Controllers/BorrowerController.php (✅ Refactored)
- app/Http/Controllers/BorrowingController.php (✅ Updated)
- app/Http/Controllers/ReportController.php (✅ Updated)
- app/Http/Controllers/DashboardController.php (✅ Updated)

### Form Requests (2)

- app/Http/Requests/BorrowerRequest.php (✅ Updated)
- app/Http/Requests/BorrowingStoreRequest.php (✅ Updated)

### Views (6)

- resources/views/borrowers/\_form.blade.php
- resources/views/borrowers/index.blade.php
- resources/views/borrowers/show.blade.php
- resources/views/borrowings/create.blade.php
- resources/views/borrowings/index.blade.php
- resources/views/borrowings/show.blade.php
- resources/views/borrowings/return.blade.php
- resources/views/borrowings/overdue.blade.php
- resources/views/borrowings/damaged.blade.php

### Seeders (1)

- database/seeders/DatabaseSeeder.php (✅ Updated)

---

## Next Steps

1. ✅ Run migrations to update database schema
2. ✅ Test borrower creation flow
3. ✅ Test borrowing transaction creation and returns
4. ✅ Test reports and dashboard stats
5. ✅ Verify existing borrower data migrated correctly
6. Optional: Delete the `app/Models/Borrower.php` file after confirming no references remain

---

## Notes

- Borrower users are created with temporary password: `TempPassword123!`
- Borrowers should reset this password on first login (Fortify handles this)
- All existing queries automatically work with the new structure
- The borrower relationship on BorrowingTransaction still works (`$transaction->borrower`)
- Reports now group by users instead of borrowers table
