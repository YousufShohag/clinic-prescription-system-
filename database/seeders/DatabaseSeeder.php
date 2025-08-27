<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }

    public function run(): void
{
    // Create 10 categories
    $categories = \App\Models\Category::factory(10)->create();

    // Create 15,000 medicines assigned to those categories
    \App\Models\Medicine::factory(15000)->make()->each(function ($medicine) use ($categories) {
        $medicine->category_id = $categories->random()->id;
        $medicine->save();
    });
}

}
