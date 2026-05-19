<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Product::create([
            'name' => 'iPhone 15',
            'description' => 'Apple Mobile',
            'image' => 'products/37Q4Wk4RMfFMilsG74rKI38Jnf0wKiZeOwzUm30x.jpg',
            'price' => 50000.00,
        ]);

        Product::create([
            'name' => 'Samsung S24',
            'description' => 'Samsung Mobile',
            'image' => 'products/GIGbBMIL3VWA5uNDoSNHb3p53zyTCnxu6x5FpYJz/s24.jpg',
            'price' => 42000.00,
        ]);

        Product::create([
            'name' => 'مياه رذاذ',
            'description' => 'مياه معدنية',
            'image' => 'products/yZnssX0RGXt169g1A9PBJzTMkSqerg0ZR6GxSKax/rzaz.jpg',
            'price' => 20.00,
        ]);
    }
}
