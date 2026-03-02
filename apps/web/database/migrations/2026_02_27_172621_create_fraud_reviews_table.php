<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['approved', 'rejected', 'escalated']);
            $table->text('reason')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();

            $table->index(['entry_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_reviews');
    }
};
