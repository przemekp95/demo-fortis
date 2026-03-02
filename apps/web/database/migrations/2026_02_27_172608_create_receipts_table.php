<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number');
            $table->decimal('purchase_amount', 10, 2);
            $table->date('purchase_date');
            $table->string('device_fingerprint', 128)->nullable();
            $table->ipAddress('submitted_ip')->nullable();
            $table->enum('status', ['submitted', 'accepted', 'rejected', 'flagged'])->default('submitted');
            $table->text('validation_notes')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'user_id']);
            $table->unique(['campaign_id', 'receipt_number', 'purchase_date', 'purchase_amount'], 'receipt_unique_per_campaign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
