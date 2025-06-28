<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    //     Category::insert([
    //         [
    //             "title"       => "Burgers",
    //             "description" => "Juicy burgers made with love",
    //             "image_url"   => "assets/categories_images/burger_image2.jpg",
    //         ],
    //         [
    //             "title"       => "Pasta",
    //             "description" => "Delicious pasta dishes made with love",
    //             "image_url"   => "assets/categories_images/pasta_image.jpg",
    //         ],
    //         [
    //             "title" => "Pizzas",
    //             "description" => "Hot and fresh pizzas made with love",
    //             "image_url" => "assets/categories_images/pizza_image.jpg",
    //         ],
    //         [
    //             "title" => "Sandwiches",
    //             "description" => "Fresh and delicious sandwiches made with love",
    //             "image_url" => "assets/categories_images/sandwich_image.jpg",
    //         ]
    //     ]);
    //     Category::factory()->count(10)->create();

     $categories = [
            'Grills',
            'Appetizers',
            'Desserts',
            'Soups',
            'Fast Food',
        ];

        foreach ($categories as $categoryTitle) {
            $imageName = strtolower(str_replace(' ', '_', $categoryTitle)) . '.jpg';
            Category::create([
                'title' => $categoryTitle,
                'description' => $categoryTitle . ' category',
                'image_url' => asset("assets/$imageName"),
            ]);
        }
    }
}
