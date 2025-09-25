<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing plans\n        \Illuminate\\Support\\Facades\\Schema::disableForeignKeyConstraints();\n        Plan::truncate();\n        \Illuminate\\Support\\Facades\\Schema::enableForeignKeyConstraints();

        $plansData = [
            ['name' => 'Starter', 'price' => 29.99, 'interval' => 'month', 'registers' => 1, 'users' => 1, 'inventory_management' => false, 'reports' => false],
            ['name' => 'Starter', 'price' => 299.99, 'interval' => 'year', 'registers' => 1, 'users' => 1, 'inventory_management' => false, 'reports' => false],
            ['name' => 'Business', 'price' => 79.99, 'interval' => 'month', 'registers' => 3, 'users' => 5, 'inventory_management' => true, 'reports' => true],
            ['name' => 'Business', 'price' => 799.99, 'interval' => 'year', 'registers' => 3, 'users' => 5, 'inventory_management' => true, 'reports' => true],
            ['name' => 'Enterprise', 'price' => 199.99, 'interval' => 'month', 'registers' => 10, 'users' => 20, 'inventory_management' => true, 'reports' => true],
            ['name' => 'Enterprise', 'price' => 1999.99, 'interval' => 'year', 'registers' => 10, 'users' => 20, 'inventory_management' => true, 'reports' => true],
        ];

        foreach ($plansData as $data) {
            Plan::create($data);
        }
    }
}
