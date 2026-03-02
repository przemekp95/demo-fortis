<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_retention_jobs', function (Blueprint $table): void {
            $table->id();
            $table->enum('type', ['anonymize', 'delete']);
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->timestamp('target_date');
            $table->json('metadata')->nullable();
            $table->timestamp('ran_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'target_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_retention_jobs');
    }
};
