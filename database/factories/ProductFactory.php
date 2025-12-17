<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Apple MacBook Pro 14-inch',
            'Samsung Galaxy S23 Ultra',
            'Sony WH-1000XM5 Headphones',
            'Dell XPS 13 Laptop',
            'Logitech MX Master 3S Mouse',
            'HP LaserJet Pro Printer',
            'Anker PowerCore 20000mAh',
            'iPhone 15 Pro Max',
            'iPhone 14 Pro Max',
            'iPhone 13 Pro Max',
            'iPhone 12 Pro Max',
            'iPhone 11 Pro Max',
            'iPhone X Pro Max',
            'iPhone 8 Pro',
            'iPhone 7 S',
            'Canon EOS R8 Mirrorless Camera',
            'LG UltraWide 34-inch Monitor',
        ];

        return [
            'name' => $this->faker->randomElement($products),
            'price' => $this->faker->numberBetween(100, 5000),
            'description' => $this->faker->sentence(50),
        ];
    }
}
