<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manager_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('submission_id')->constrained('task_submissions')->onDelete('cascade');
            $table->integer('quality_score')->nullable();
            $table->integer('timeliness_score')->nullable();
            $table->text('feedback')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->string('evaluation_type')->nullable();
            $table->json('evaluation_criteria')->nullable();
            $table->boolean('needs_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manager_evaluations');
    }
};
