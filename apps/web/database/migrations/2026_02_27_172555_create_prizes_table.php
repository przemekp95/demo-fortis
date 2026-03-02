<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('draw_type', ['daily', 'final']);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('value', 10, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'draw_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
