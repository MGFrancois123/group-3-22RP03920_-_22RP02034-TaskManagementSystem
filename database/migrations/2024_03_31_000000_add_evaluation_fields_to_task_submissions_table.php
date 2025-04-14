<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->integer('revision_count')->default(0)->after('attempt_number');
            $table->boolean('is_final')->default(false)->after('revision_count');
            $table->string('review_status')->default('pending')->after('is_final');
        });
    }

    public function down()
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'revision_count',
                'is_final',
                'review_status'
            ]);
        });
    }
};
