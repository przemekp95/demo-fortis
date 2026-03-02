<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_signals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
            $table->string('signal_type');
            $table->decimal('score', 5, 2);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['entry_id', 'signal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_signals');
    }
};
