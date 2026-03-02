<?php

namespace Database\Seeders;

use App\Models\ConsentVersion;
use Illuminate\Database\Seeder;

class ConsentVersionSeeder extends Seeder
{
    public function run(): void
    {
        $consents = [
            [
                'code' => 'terms',
                'version' => 1,
                'label' => 'Akceptacja regulaminu loterii',
                'content' => 'Regulamin loterii Fortis v1',
            ],
            [
                'code' => 'privacy',
                'version' => 1,
                'label' => 'Akceptacja polityki prywatności',
                'content' => 'Polityka prywatności Fortis v1',
            ],
        ];

        foreach ($consents as $consent) {
            ConsentVersion::updateOrCreate(
                ['code' => $consent['code'], 'version' => $consent['version']],
                [
                    'label' => $consent['label'],
                    'content' => $consent['content'],
                    'is_active' => true,
                    'published_at' => now(),
                ],
            );
        }
    }
}
