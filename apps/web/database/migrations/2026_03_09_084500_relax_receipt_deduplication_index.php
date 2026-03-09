<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table): void {
            $table->dropUnique('receipt_unique_per_campaign');
            $table->index(
                ['campaign_id', 'receipt_number', 'purchase_date', 'purchase_amount'],
                'receipt_lookup_per_campaign',
            );
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table): void {
            $table->dropIndex('receipt_lookup_per_campaign');
            $table->unique(
                ['campaign_id', 'receipt_number', 'purchase_date', 'purchase_amount'],
                'receipt_unique_per_campaign',
            );
        });
    }
};
