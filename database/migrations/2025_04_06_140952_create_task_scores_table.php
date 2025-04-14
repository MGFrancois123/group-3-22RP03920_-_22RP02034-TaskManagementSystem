<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quality_score')->nullable();
            $table->integer('timeliness_score')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            // Ensure a user can only have one score per task
            $table->unique(['task_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_scores');
    }
};
