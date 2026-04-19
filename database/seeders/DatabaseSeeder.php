<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(['email' => 'admin@jobaggregator.com'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create test user
        User::firstOrCreate(['email' => 'user@jobaggregator.com'], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create categories (firstOrCreate to avoid duplicates)
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Data Science', 'slug' => 'data-science'],
            ['name' => 'Design', 'slug' => 'design'],
            ['name' => 'Product Management', 'slug' => 'product-management'],
            ['name' => 'QA & Testing', 'slug' => 'qa-testing'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Customer Support', 'slug' => 'customer-support'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // No fake jobs — run `php artisan jobs:scrape` to populate real listings
    }
}
