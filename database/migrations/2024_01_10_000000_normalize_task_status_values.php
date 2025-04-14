<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update any variations of status values to our standardized format
        DB::table('tasks')->where('status', 'like', '%pending%')
            ->orWhere('status', 'like', '%Pending%')
            ->update(['status' => Task::STATUS_PENDING]);

        DB::table('tasks')->where('status', 'like', '%progress%')
            ->orWhere('status', 'like', '%Progress%')
            ->orWhere('status', 'like', '%in-progress%')
            ->update(['status' => Task::STATUS_IN_PROGRESS]);

        DB::table('tasks')->where('status', 'like', '%complete%')
            ->orWhere('status', 'like', '%Complete%')
            ->update(['status' => Task::STATUS_COMPLETED]);

        // Add check constraint to ensure only valid status values are used
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(255)");
        }
    }
};
