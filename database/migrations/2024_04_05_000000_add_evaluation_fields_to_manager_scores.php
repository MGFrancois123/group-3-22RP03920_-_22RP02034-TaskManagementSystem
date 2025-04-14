<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluationFieldsToManagerScores extends Migration
{
    public function up()
    {
        Schema::table('manager_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('manager_scores', 'communication_score')) {
                $table->integer('communication_score')->nullable();
            }
            if (!Schema::hasColumn('manager_scores', 'initiative_score')) {
                $table->integer('initiative_score')->nullable();
            }
            if (!Schema::hasColumn('manager_scores', 'strengths')) {
                $table->text('strengths')->nullable();
            }
            if (!Schema::hasColumn('manager_scores', 'areas_for_improvement')) {
                $table->text('areas_for_improvement')->nullable();
            }
            if (!Schema::hasColumn('manager_scores', 'performance_level')) {
                $table->string('performance_level')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('manager_scores', function (Blueprint $table) {
            $table->dropColumn([
                'communication_score',
                'initiative_score',
                'strengths',
                'areas_for_improvement',
                'performance_level'
            ]);
        });
    }
}
