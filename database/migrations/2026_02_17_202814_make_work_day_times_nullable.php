<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Drop Foreign Key on time_entries
        try {
            Schema::table('time_entries', function (Blueprint $table) {
                $table->dropForeign(['work_day_id']);
            });
        }
        catch (\Exception $e) {
        // Ignore if FK doesn't exist (likely from previous failed run)
        }

        // 0a. Drop work_days_v2 if it exists (cleanup from failed migration)
        Schema::dropIfExists('work_days_v2');

        // 1. Create new table with nullable columns
        Schema::create('work_days_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('break_duration')->nullable(); // in minutes
            $table->timestamps();
        });

        // 2. Copy data
        DB::statement('INSERT INTO work_days_v2 (id, user_id, date, start_time, end_time, break_duration, created_at, updated_at) SELECT id, user_id, date, start_time, end_time, break_duration, created_at, updated_at FROM work_days');

        // 3. Drop old table
        Schema::drop('work_days');

        // 4. Rename new table
        Schema::rename('work_days_v2', 'work_days');

        // 5. Restore Foreign Key
        Schema::table('time_entries', function (Blueprint $table) {
            $table->foreign('work_day_id')->references('id')->on('work_days')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 0. Drop Foreign Key
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropForeign(['work_day_id']);
        });

        // 0a. Cleanup
        Schema::dropIfExists('work_days_v2');

        // 1. Create original table
        Schema::create('work_days_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->default('08:00');
            $table->time('end_time')->default('16:30');
            $table->integer('break_duration')->default(30); // in minutes
            $table->timestamps();
        });

        // 2. Copy data
        DB::statement("INSERT INTO work_days_v2 (id, user_id, date, start_time, end_time, break_duration, created_at, updated_at) 
                       SELECT id, user_id, date, COALESCE(start_time, '08:00'), COALESCE(end_time, '16:30'), COALESCE(break_duration, 30), created_at, updated_at FROM work_days");

        // 3. Drop current table
        Schema::drop('work_days');

        // 4. Rename
        Schema::rename('work_days_v2', 'work_days');

        // 5. Restore Foreign Key
        Schema::table('time_entries', function (Blueprint $table) {
            $table->foreign('work_day_id')->references('id')->on('work_days')->cascadeOnDelete();
        });
    }
};
