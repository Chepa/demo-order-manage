<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductCacheService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = ['engine', 'brakes', 'suspension', 'electrical', 'exhaust', 'transmission'];

        foreach ($categories as $category) {
            Product::factory(5)->category($category)->create();
        }

        Customer::factory(10)->create();

        app(ProductCacheService::class)->invalidate();
    }
}
