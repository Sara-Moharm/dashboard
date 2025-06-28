<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            "title" => $this->faker->unique()->words(2, true),
            "description" => $this->faker->text(100),
            "image_url" => $this->faker->imageUrl(200, 200),
        ];
    }

    
    public function configure()
    {
        return $this->afterCreating(function ($category) {
            \App\Models\CategoryDish::factory()->count(3)->create([
                'category_id' => $category->id,
            ]);
        });
    }
}
