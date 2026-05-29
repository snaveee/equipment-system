<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, add the user_id column to borrowing_transactions
        Schema::table('borrowing_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowing_transactions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            }
        });

        // Then migrate data: create users from borrowers
        $borrowers = \DB::table('borrowers')->get();
        foreach ($borrowers as $borrower) {
            \DB::table('users')->insert([
                'name' => $borrower->full_name,
                'email' => $borrower->email,
                'password' => \Illuminate\Support\Facades\Hash::make('TempPassword123!'),
                'role' => 'borrower',
                'department' => $borrower->department,
                'position' => $borrower->position,
                'contact_number' => $borrower->contact_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update borrowing_transactions to use user_id instead of borrower_id
        \DB::statement('
            UPDATE borrowing_transactions
            SET user_id = (
                SELECT users.id
                FROM users
                JOIN borrowers ON users.email = borrowers.email
                WHERE borrowers.id = borrowing_transactions.borrower_id
            )
            WHERE borrower_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // Delete the borrower users from users table
        \DB::table('users')->where('role', 'borrower')->delete();

        // Reset user_id to NULL
        \DB::table('borrowing_transactions')->update(['user_id' => null]);

        // Drop user_id column
        Schema::table('borrowing_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('borrowing_transactions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
