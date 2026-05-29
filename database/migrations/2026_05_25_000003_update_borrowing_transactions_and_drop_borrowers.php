<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old borrower_id foreign key and column from borrowing_transactions
        Schema::table('borrowing_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('borrowing_transactions', 'borrower_id')) {
                $table->dropForeign(['borrower_id']);
                $table->dropColumn('borrower_id');
            }
        });

        // Drop the borrowers table
        Schema::dropIfExists('borrowers');
    }

    public function down(): void
    {
        // Recreate borrowers table
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('department');
            $table->string('position');
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->timestamps();
        });

        // Restore borrower_id column
        Schema::table('borrowing_transactions', function (Blueprint $table) {
            $table->foreignId('borrower_id')->after('id')->constrained('borrowers')->cascadeOnDelete();
        });
    }
};
