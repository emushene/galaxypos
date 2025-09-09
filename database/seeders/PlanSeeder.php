<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'Basic Shop',
                'price' => 19.99,
                'interval' => 'monthly',
                'registers' => 1,
                'users' => 1,
                'inventory_management' => true,
                'reports' => false,
            ],
            [
                'name' => 'Retail Plus',
                'price' => 49.99,
                'interval' => 'monthly',
                'registers' => 3,
                'users' => 5,
                'inventory_management' => true,
                'reports' => true,
            ],
            [
                'name' => 'Enterprise',
                'price' => 99.99,
                'interval' => 'monthly',
                'registers' => 10,
                'users' => 20,
                'inventory_management' => true,
                'reports' => true,
            ]
        ]);
    
    }
}
