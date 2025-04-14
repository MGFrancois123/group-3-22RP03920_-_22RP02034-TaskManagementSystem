<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update the role column to include 'manager'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'user') DEFAULT 'user'");

        // Then add performance tracking fields
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('average_score', 5, 2)->default(0)->after('role');
            $table->integer('tasks_completed')->default(0)->after('average_score');
            $table->integer('tasks_on_time')->default(0)->after('tasks_completed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['average_score', 'tasks_completed', 'tasks_on_time']);
        });

        // Revert role column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
    }
}; 