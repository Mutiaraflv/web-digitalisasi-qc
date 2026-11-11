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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->string('action', 60);
            $table->string('subject_label', 120)->nullable();
            $table->string('description');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action']);
        });

        // Backfill initial activity logs from existing reports so timeline is populated immediately
        $reports = DB::table('reports')->orderBy('created_at')->get();
        foreach ($reports as $report) {
            $submitAt = $report->submitted_at ?? $report->created_at ?? now();
            if ($report->review_status === 'draft') {
                DB::table('activity_logs')->insert([
                    'user_id' => $report->created_by,
                    'report_id' => $report->id,
                    'action' => 'report_draft_created',
                    'subject_label' => $report->ssc_id,
                    'description' => "Menyimpan draft laporan {$report->ssc_id}",
                    'meta' => json_encode(['part_name' => $report->part_name, 'status' => 'draft']),
                    'created_at' => $report->created_at ?? now(),
                ]);
            } else {
                DB::table('activity_logs')->insert([
                    'user_id' => $report->created_by,
                    'report_id' => $report->id,
                    'action' => 'report_submitted',
                    'subject_label' => $report->ssc_id,
                    'description' => "Mensubmit laporan {$report->ssc_id} (Waiting Review)",
                    'meta' => json_encode(['part_name' => $report->part_name, 'status' => 'waiting_review']),
                    'created_at' => $submitAt,
                ]);

                if ($report->review_status === 'reviewed') {
                    DB::table('activity_logs')->insert([
                        'user_id' => $report->reviewed_by ?? $report->reviewer_id,
                        'report_id' => $report->id,
                        'action' => 'report_reviewed',
                        'subject_label' => $report->ssc_id,
                        'description' => "Menyelesaikan review (Finish Review) laporan {$report->ssc_id}",
                        'meta' => json_encode(['part_name' => $report->part_name, 'status' => 'reviewed']),
                        'created_at' => $report->reviewed_at ?? $report->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

