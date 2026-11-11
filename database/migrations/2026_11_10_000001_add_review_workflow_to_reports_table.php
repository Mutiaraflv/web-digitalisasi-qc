<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->enum('review_status', ['waiting_review', 'reviewed'])->default('waiting_review')->after('has_rejects');
            $table->timestamp('submitted_at')->nullable()->after('review_status');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['reviewer_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'created_by',
                'reviewer_id',
                'review_status',
                'submitted_at',
                'reviewed_at',
                'reviewed_by',
            ]);
        });
    }
};
