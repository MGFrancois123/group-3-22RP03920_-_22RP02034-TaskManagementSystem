<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add priority field
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
            
            // Add estimated completion time
            $table->integer('estimated_hours')->nullable()->after('priority');
            
            // Add actual completion time
            $table->integer('actual_hours')->nullable()->after('estimated_hours');
            
            // Add task category
            $table->string('category')->nullable()->after('actual_hours');
            
            // Add task tags
            $table->json('tags')->nullable()->after('category');
            
            // Add task attachments
            $table->json('attachments')->nullable()->after('tags');
            
            // Add task dependencies
            $table->json('dependencies')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'estimated_hours',
                'actual_hours',
                'category',
                'tags',
                'attachments',
                'dependencies'
            ]);
        });
    }
}; 