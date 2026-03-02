<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draw_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('draw_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['daily', 'final']);
            $table->string('idempotency_key')->unique();
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'type', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draw_runs');
    }
};
