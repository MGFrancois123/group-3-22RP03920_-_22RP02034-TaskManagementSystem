<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add average_score to task_submissions table
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->decimal('average_score', 5, 2)->nullable()->after('timeliness_score');
        });

        // Add average_score to manager_evaluations table
        Schema::table('manager_evaluations', function (Blueprint $table) {
            $table->decimal('average_score', 5, 2)->nullable()->after('timeliness_score');
        });

        // Add average_score to task_scores table
        Schema::table('task_scores', function (Blueprint $table) {
            $table->decimal('average_score', 5, 2)->nullable()->after('timeliness_score');
        });
    }

    public function down()
    {
        // Remove average_score from task_submissions table
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->dropColumn('average_score');
        });

        // Remove average_score from manager_evaluations table
        Schema::table('manager_evaluations', function (Blueprint $table) {
            $table->dropColumn('average_score');
        });

        // Remove average_score from task_scores table
        Schema::table('task_scores', function (Blueprint $table) {
            $table->dropColumn('average_score');
        });
    }
};
