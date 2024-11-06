<?php

namespace Database\Factories;

use App\Models\Category;
use App\Services\CategoryService;
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
        $categories = Category::inRandomOrder()->first();

        return [
            'name' => fake()->name(),
            'price' => fake()->randomFloat(2, 0, 10000),
            'description' => fake()->text(),
            'category' => $categories ? $categories->name : fake()->word(),
            'image_url' => fake()->imageUrl(),
        ];
    }
}
