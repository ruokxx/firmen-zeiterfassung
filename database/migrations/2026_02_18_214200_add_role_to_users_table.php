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
        // Add role column if it doesn't exist
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // Determine position: after email or password
                $after = Schema::hasColumn('users', 'is_active') ? 'is_active' : 'email';
                $table->string('role')->default('employee')->after($after);
            });
        }

        // Migrate data from is_admin to role
        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        // Should we drop is_admin?
        // User requested "admin roles should not be changed" during update.
        // Safest bet is to KEEP the column for now to avoid data loss if rollback is needed.
        // But we can mark it as nullable or ignore it. 
        // Let's NOT drop it to be 100% safe as per user request.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            // Restore is_admin from role before dropping?
            if (Schema::hasColumn('users', 'is_admin')) {
                DB::table('users')->where('role', 'admin')->update(['is_admin' => true]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
