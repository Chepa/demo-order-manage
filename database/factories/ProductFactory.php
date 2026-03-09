<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    private static array $categories = [
        'engine',
        'brakes',
        'suspension',
        'electrical',
        'exhaust',
        'transmission',
    ];

    public function definition(): array
    {
        $categoryName = fake()->randomElement(self::$categories);

        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['slug' => str($categoryName)->slug()]
        );

        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'price' => fake()->randomFloat(2, 50, 5000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'category_id' => $category->id,
        ];
    }

    public function category(string $category): static
    {
        return $this->state(function (array $attributes) use ($category) {
            $categoryModel = Category::firstOrCreate(
                ['name' => $category],
                ['slug' => str($category)->slug()]
            );

            return ['category_id' => $categoryModel->id];
        });
    }
}
