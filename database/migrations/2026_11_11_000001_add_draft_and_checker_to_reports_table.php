<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('checker_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        DB::statement("ALTER TABLE reports MODIFY COLUMN review_status ENUM('draft', 'waiting_review', 'reviewed') NOT NULL DEFAULT 'waiting_review'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reports')->where('review_status', 'draft')->update(['review_status' => 'waiting_review']);
        DB::statement("ALTER TABLE reports MODIFY COLUMN review_status ENUM('waiting_review', 'reviewed') NOT NULL DEFAULT 'waiting_review'");

        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['checker_id']);
            $table->dropColumn('checker_id');
        });
    }
};

