<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ConsentVersionSeeder::class,
            ApiClientSeeder::class,
            CampaignSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@fortis.test'],
            [
                'name' => 'Fortis Admin',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
            ],
        );

        $admin->profile()->updateOrCreate([], [
            'first_name' => 'Fortis',
            'last_name' => 'Admin',
            'phone' => '+48111111111',
            'address_line_1' => 'Admin Street 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'PL',
            'birth_date' => '1990-01-01',
        ]);

        $admin->assignRole('admin');

        $participant = User::updateOrCreate(
            ['email' => 'participant@fortis.test'],
            [
                'name' => 'Fortis Participant',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
            ],
        );

        $participant->profile()->updateOrCreate([], [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'phone' => '+48222222222',
            'address_line_1' => 'Uczestnika 10',
            'city' => 'Krakow',
            'postal_code' => '30-001',
            'country' => 'PL',
            'birth_date' => '1992-02-02',
        ]);

        $participant->assignRole('participant');
    }
}
