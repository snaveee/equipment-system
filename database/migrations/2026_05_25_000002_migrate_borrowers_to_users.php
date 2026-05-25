<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing borrowers to users table
        $borrowers = DB::table('borrowers')->get();

        foreach ($borrowers as $borrower) {
            DB::table('users')->insert([
                'name' => $borrower->full_name,
                'email' => $borrower->email,
                'password' => Hash::make('TempPassword123!'), // Temporary password for first login
                'role' => 'borrower',
                'department' => $borrower->department,
                'position' => $borrower->position,
                'contact_number' => $borrower->contact_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update foreign key references in borrowing_transactions
        // from borrower_id to user_id
        DB::statement('
            UPDATE borrowing_transactions
            SET user_id = (
                SELECT users.id
                FROM users
                JOIN borrowers ON users.email = borrowers.email
                WHERE borrowers.id = borrowing_transactions.borrower_id
            )
            WHERE user_id IS NULL
        ');
    }

    public function down(): void
    {
        // This is a data migration, so rollback is manual:
        // Delete the borrower users from users table
        DB::table('users')->where('role', 'borrower')->delete();
    }
};
