<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => mb_ucfirst(fake()->words(3, true)),
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->optional()->paragraph(),
            // Цены в минорных единицах (копейках)
            'price' => fake()->numberBetween(5_000, 50_000),
            'discount_price' => null,
            'price_unit' => fake()->randomElement(['куб', 'шт', 'м2']),
            'is_volume_price' => false,
            'is_active' => true,
        ];
    }

    public function discounted(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_price' => (int) round($attributes['price'] * 0.8),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
