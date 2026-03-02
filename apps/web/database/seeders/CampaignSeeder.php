<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaign = Campaign::updateOrCreate(
            ['slug' => 'fortis-loteria-2026'],
            [
                'name' => 'Fortis Loteria 2026',
                'status' => 'active',
                'description' => 'Zgłoś paragon i wygraj nagrody codzienne oraz finałowe.',
                'timezone' => 'Europe/Warsaw',
                'starts_at' => Carbon::now()->subDay(),
                'ends_at' => Carbon::now()->addDays(14),
                'final_draw_at' => Carbon::now()->addDays(15),
                'terms_url' => '/regulamin',
            ],
        );

        $campaign->rule()->updateOrCreate([], [
            'max_entries_per_day' => 5,
            'velocity_per_hour' => 3,
            'max_receipt_age_days' => 14,
            'min_purchase_amount' => 1,
            'deduplicate_receipts' => true,
            'risk_score_flag_threshold' => 60,
            'risk_score_reject_threshold' => 90,
        ]);

        $campaign->prizes()->delete();
        $campaign->prizes()->createMany([
            ['name' => 'Voucher 100 PLN', 'draw_type' => 'daily', 'quantity' => 5, 'value' => 100],
            ['name' => 'Nagroda główna 10 000 PLN', 'draw_type' => 'final', 'quantity' => 1, 'value' => 10000],
        ]);
    }
}
