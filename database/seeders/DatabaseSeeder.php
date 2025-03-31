<?php

namespace Database\Seeders;

use App\Models\HotspotPlan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        HotspotPlan::insert([
            ['name' => '1 Hour', 'price' => 5, 'duration' => 60],
            ['name' => '3 Hour', 'price' => 20, 'duration' => 180],
            ['name' => '12 Hour', 'price' => 30, 'duration' => 720],
            ['name' => '24 Hour', 'price' => 40, 'duration' => 1440],
        ]);
    }
}
