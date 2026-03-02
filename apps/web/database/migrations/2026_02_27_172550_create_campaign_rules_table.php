<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('max_entries_per_day')->default(5);
            $table->unsignedInteger('velocity_per_hour')->default(3);
            $table->unsignedInteger('max_receipt_age_days')->default(14);
            $table->decimal('min_purchase_amount', 10, 2)->default(1);
            $table->boolean('deduplicate_receipts')->default(true);
            $table->decimal('risk_score_flag_threshold', 5, 2)->default(60);
            $table->decimal('risk_score_reject_threshold', 5, 2)->default(90);
            $table->json('extra_rules')->nullable();
            $table->timestamps();

            $table->unique('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_rules');
    }
};
