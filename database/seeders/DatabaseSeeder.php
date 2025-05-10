<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed application roles
        Role::firstOrCreate(['name' => 'Developer']);
        Role::firstOrCreate(['name' => 'Landlord']);
        Role::firstOrCreate(['name' => 'Tenant']);
        Role::firstOrCreate(['name' => 'Secretary']);

        // Create initial developer user
        $dev = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $dev->assignRole('Developer');

        // Seed subscription plans
        \App\Models\SubscriptionPlan::firstOrCreate(
            ['name' => 'Pilot (Free)'],
            ['price' => 0, 'duration_months' => 1, 'property_limit' => 5, 'unit_limit' => 20, 'features' => json_encode(['email_sms_reminders','basic_analytics'])]
        );
        \App\Models\SubscriptionPlan::firstOrCreate(
            ['name' => 'Basic'],
            ['price' => 1000, 'duration_months' => 1, 'property_limit' => 10, 'unit_limit' => 50, 'features' => json_encode(['mpesa_paypal','auto_invoicing','standard_analytics'])]
        );
        \App\Models\SubscriptionPlan::firstOrCreate(
            ['name' => 'Standard'],
            ['price' => 3000, 'duration_months' => 1, 'property_limit' => 50, 'unit_limit' => 200, 'features' => json_encode(['manual_billing','reviews','custom_branding','reports'])]
        );
        \App\Models\SubscriptionPlan::firstOrCreate(
            ['name' => 'Premium'],
            ['price' => 6000, 'duration_months' => 1, 'property_limit' => -1, 'unit_limit' => -1, 'features' => json_encode(['white_label','api_access','advanced_analytics','priority_support'])]
        );

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
