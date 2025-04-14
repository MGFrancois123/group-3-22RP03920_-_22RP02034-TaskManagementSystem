<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('manager_scores', function (Blueprint $table) {
            $table->text('evaluation_notes')->nullable()->after('feedback');
            $table->string('evaluation_type')->default('regular')->after('evaluation_notes');
            $table->json('evaluation_criteria')->nullable()->after('evaluation_type');
            $table->boolean('needs_follow_up')->default(false)->after('evaluation_criteria');
            $table->date('follow_up_date')->nullable()->after('needs_follow_up');
        });
    }

    public function down()
    {
        Schema::table('manager_scores', function (Blueprint $table) {
            $table->dropColumn([
                'evaluation_notes',
                'evaluation_type',
                'evaluation_criteria',
                'needs_follow_up',
                'follow_up_date'
            ]);
        });
    }
};
