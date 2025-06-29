<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryDish>
 */
class CategoryDishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title"       => $this->faker->unique()->words(2, true),
            "description" => $this->faker->text(100),
            "price"       => $this->faker->randomFloat(2, 100,1000),
            "image_url"   => $this->faker->imageUrl(200, 200),
            "category_id" => \App\Models\Category::inRandomOrder()->first()?->id,
            "calories"   =>  $this->faker->randomFloat(2, 100,1000),
        ];
    }
}
