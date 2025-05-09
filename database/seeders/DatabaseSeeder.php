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

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
