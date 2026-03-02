<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->string('timezone')->default('Europe/Warsaw');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('final_draw_at');
            $table->string('terms_url')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
