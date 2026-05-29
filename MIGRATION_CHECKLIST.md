# Pre-Migration Verification Checklist

## System Changes Verification

Before running the migrations, verify all changes have been implemented:

### ✅ Migrations

- [x] `2026_05_25_000001_add_profile_fields_to_users_table.php` - Adds profile fields to users
- [x] `2026_05_25_000002_migrate_borrowers_to_users.php` - Migrates borrower data to users
- [x] `2026_05_25_000003_update_borrowing_transactions_and_drop_borrowers.php` - Updates transactions & drops borrowers table

### ✅ Models Updated

- [x] `User.php` - Added fields, transactions() relationship, isBorrower() method
- [x] `BorrowingTransaction.php` - Changed borrower_id to user_id in $fillable

### ✅ Controllers Refactored/Updated

- [x] `BorrowerController.php` - Now manages Users with role='borrower'
- [x] `BorrowingController.php` - Uses User model for borrower selection
- [x] `ReportController.php` - Queries users table instead of borrowers
- [x] `DashboardController.php` - Removed Borrower model reference

### ✅ Form Requests Updated

- [x] `BorrowerRequest.php` - Uses 'name' field, validates against users table
- [x] `BorrowingStoreRequest.php` - Uses 'user_id', validates borrower role

### ✅ Views Updated (10 files)

- [x] `borrowers/_form.blade.php` - Updated full_name → name, added password fields
- [x] `borrowers/index.blade.php` - Updated full_name → name
- [x] `borrowers/show.blade.php` - Updated full_name → name
- [x] `borrowings/create.blade.php` - Updated borrower_id → user_id, full_name → name
- [x] `borrowings/index.blade.php` - Updated full_name → name
- [x] `borrowings/show.blade.php` - Updated full_name → name
- [x] `borrowings/return.blade.php` - Updated full_name → name
- [x] `borrowings/overdue.blade.php` - Updated full_name → name
- [x] `borrowings/damaged.blade.php` - Updated full_name → name

### ✅ Seeder Updated

- [x] `DatabaseSeeder.php` - Creates borrower users, uses user_id in transactions

---

## Migration Execution Steps

### Step 1: Backup Database

```bash
# Create a backup before running migrations
# For MySQL: mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Run Migrations

```bash
cd c:\Users\snaaa\backend\laravel\equipment-system
php artisan migrate
```

### Step 3: Verify Migration Success

```bash
php artisan tinker

# Check users table has new fields
>>> \App\Models\User::first()

# Check borrower users were created
>>> \App\Models\User::where('role', 'borrower')->count()

# Check borrowing_transactions have user_id (should be 5 from seeder)
>>> \App\Models\BorrowingTransaction::first()

# Check borrowers table no longer exists
>>> DB::statement("SHOW TABLES LIKE 'borrowers'")  // Should return empty
```

### Step 4: Test the Application

- [ ] Log in as admin/staff
- [ ] Navigate to Borrowers page
- [ ] Create a new borrower
- [ ] Create a borrowing transaction
- [ ] Process a return
- [ ] View reports
- [ ] Check dashboard statistics

---

## Rollback (If Needed)

If something goes wrong, you can rollback:

```bash
php artisan migrate:rollback --step=3
```

This will reverse the last 3 migrations (all equipment-system changes).

---

## Production Deployment Checklist

- [ ] All changes tested in development
- [ ] Database backup created
- [ ] Migrations run successfully
- [ ] All existing borrower data migrated (verified borrower user count matches)
- [ ] No errors in Laravel logs
- [ ] Borrowers can log in and reset password
- [ ] Borrowing transactions work correctly
- [ ] Reports generate correctly
- [ ] Dashboard shows correct statistics
- [ ] No broken references in views or controllers

---

## Post-Migration Cleanup (Optional)

After confirming everything works:

```bash
# Delete deprecated Borrower model (keeping for reference for now)
# rm app/Models/Borrower.php

# Clear any cached files
php artisan view:clear
php artisan config:clear
```

---

## Testing Scenarios

### Scenario 1: Create New Borrower

1. Log in as admin
2. Go to Borrowers > Add Borrower
3. Fill in: Name, Department, Position, Contact, Email
4. Submit
5. Verify: New borrower appears in list with role='borrower'
6. Verify: Temporary password email sent (if configured)

### Scenario 2: Create Borrowing Transaction

1. Log in as admin/staff
2. Go to Borrowing Transactions > New Transaction
3. Select a borrower (should be from users with role='borrower')
4. Select available equipment
5. Fill in details
6. Submit
7. Verify: Transaction created with user_id (not borrower_id)

### Scenario 3: Process Return

1. Go to Borrowing Transactions
2. Find an active transaction
3. Click "Return"
4. Fill in return details
5. Submit
6. Verify: Equipment status updated, transaction marked as returned

### Scenario 4: View Reports

1. Go to Reports
2. Verify: "Borrowing by Department" shows users' departments
3. Verify: Export CSV shows borrower names correctly

### Scenario 5: Dashboard Stats

1. Go to Dashboard
2. Verify: "Total Borrowers" count is correct (should be 5 from seeder)
3. Verify: All other stats display correctly

---

## Troubleshooting

### Issue: Migration fails with "Unknown column 'borrower_id'"

- **Cause**: Migrations ran out of order
- **Fix**: Ensure migration files are in chronological order, rollback and try again

### Issue: "No query results found for model"

- **Cause**: Code trying to access Borrower model that doesn't exist
- **Fix**: Verify all controllers updated to use User model

### Issue: Borrower select dropdown empty

- **Cause**: No users with role='borrower' exist
- **Fix**: Create a borrower user or run seeder: `php artisan db:seed`

### Issue: "SQLSTATE[HY000]: General error: 1 table borrowers already exists"

- **Cause**: Trying to recreate borrowers table during rollback
- **Fix**: This is expected in down() method, proceed with migration

---

## Questions or Issues?

If you encounter any issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Review the IMPLEMENTATION_SUMMARY.md for details
3. Verify all files were modified correctly
4. Ensure no other equipment-system projects interfere (separate databases)

---

## Files to Review Before Migration

Critical files to double-check:

1. ✅ [2026_05_25_000001_add_profile_fields_to_users_table.php](../../migrations/2026_05_25_000001_add_profile_fields_to_users_table.php)
2. ✅ [2026_05_25_000002_migrate_borrowers_to_users.php](../../migrations/2026_05_25_000002_migrate_borrowers_to_users.php)
3. ✅ [2026_05_25_000003_update_borrowing_transactions_and_drop_borrowers.php](../../migrations/2026_05_25_000003_update_borrowing_transactions_and_drop_borrowers.php)
4. ✅ [app/Models/User.php](../../app/Models/User.php)
5. ✅ [app/Models/BorrowingTransaction.php](../../app/Models/BorrowingTransaction.php)
6. ✅ [app/Http/Controllers/BorrowerController.php](../../app/Http/Controllers/BorrowerController.php)
7. ✅ [database/seeders/DatabaseSeeder.php](../../database/seeders/DatabaseSeeder.php)

All files are ready for migration! ✅
