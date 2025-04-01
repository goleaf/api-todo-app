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
        // For SQLite, we'll verify the columns exist instead of modifying them
        $this->ensureRoleExists('users', 'user');
        $this->ensureRoleExists('admins', 'admin');
    }

    /**
     * Helper method to ensure role column exists
     */
    private function ensureRoleExists(string $tableName, string $defaultRole): void
    {
        if (!Schema::hasColumn($tableName, 'role')) {
            Schema::table($tableName, function (Blueprint $table) use ($defaultRole) {
                $table->string('role')->default($defaultRole);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No downgrade path needed as we're just ensuring columns exist
    }
};
