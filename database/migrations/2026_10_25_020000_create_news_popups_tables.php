<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_popups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('info'); // info, warning, important, success
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('display_type')->default('once_per_user'); // once_per_user, once_per_day, always
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('news_popup_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_popup_id')->constrained('news_popups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dismissed_at');
            $table->date('last_seen_date')->nullable();
            $table->timestamps();

            $table->unique(['news_popup_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_popup_reads');
        Schema::dropIfExists('news_popups');
    }
};
