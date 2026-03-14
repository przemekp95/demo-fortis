<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draw_runs', function (Blueprint $table): void {
            $table->unique('draw_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::table('draw_runs', function (Blueprint $table): void {
            $table->dropForeign(['draw_schedule_id']);
            $table->dropUnique(['draw_schedule_id']);
            $table->index('draw_schedule_id');
            $table->foreign('draw_schedule_id')
                ->references('id')
                ->on('draw_schedules')
                ->nullOnDelete();
        });
    }
};
