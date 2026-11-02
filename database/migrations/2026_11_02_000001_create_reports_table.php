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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ssc_id')->unique();
            $table->string('report_no')->nullable();
            $table->date('date')->nullable();
            $table->string('customer')->nullable();
            $table->string('part_name')->default('OUTPUT SHAFT');
            $table->enum('inspection_type', ['Pre', 'Post'])->default('Pre');
            $table->string('job_no')->default('-');
            $table->string('model_unit')->default('HT051');
            $table->string('quote_no')->default('-');
            $table->string('part_no')->default('-');
            $table->string('doc_no')->default('FORM-QC-002');
            $table->string('rev')->default('04');
            $table->json('sections')->nullable();
            $table->json('tir')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('signatures')->nullable();
            $table->boolean('has_rejects')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

