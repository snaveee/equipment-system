<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update any 'staff' role users to 'borrower'
        DB::table('users')->where('role', 'staff')->update(['role' => 'borrower']);

        // Modify the enum column to only include 'admin' and 'borrower'
        // For MySQL, we need to use raw SQL or Laravel's modifyColumn with change()
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'borrower'])->default('borrower')->change();
        });
    }

    public function down(): void
    {
        // Revert any 'borrower' users back to 'staff' if needed
        DB::table('users')->where('role', 'borrower')->update(['role' => 'staff']);

        // Revert the enum back to include 'staff'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff'])->default('staff')->change();
        });
    }
};
