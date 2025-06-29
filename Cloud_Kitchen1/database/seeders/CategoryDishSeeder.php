<?php

namespace Database\Seeders;

use App\Models\{
    Category,
    CategoryDish
};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryDishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CategoryDish::insert([
        //     [
        //         "title"       => "Classic Cheeseburger",
        //         "description" => "A juicy beef patty topped with cheddar cheese, lettuce, tomato, onions, pickles, and special sauce — served in a toasted bun.",
        //         "price"       => 12.99,
        //         "image_url"   => "assets/category_dishes_images/classic_cheeseburger_image.jpg",
        //         "category_id" => 1,
        //     ],
        //     [
        //         "title"       => "Creamy Chicken Alfredo",
        //         "description" => "Fettuccine pasta tossed in a rich Alfredo sauce with grilled chicken, parmesan cheese, and a sprinkle of parsley.",
        //         "price"       => 14.99,
        //         "image_url"   => "assets/category_dishes_images/creamy_chicken_alfredo_image.jpg",
        //         "category_id" => 2,
        //     ],
        //     [
        //         "title"       => "Pepperoni Pizza",
        //         "description" => "Thin crust pizza with tomato sauce, mozzarella cheese, and plenty of spicy pepperoni slices — a crowd favorite!",
        //         "price"       => 10.99,
        //         "image_url"   => "assets/category_dishes_images/pepperoni_pizza_image.jpg",
        //         "category_id" => 3,
        //     ],
        //     [
        //         "title"       => "Grilled Chicken Club",
        //         "description" => "Grilled chicken breast layered with crispy bacon, lettuce, tomato, and mayo on toasted ciabatta bread.",
        //         "price"       => 11.99,
        //         "image_url"   => "assets/category_dishes_images/grilled_chicken_club_image.jpg",
        //         "category_id" => 4,
        //     ]
        // ]);
        // if(Category::count()){
        //     CategoryDish::factory()->count(10)->create();
        // }

        $dishData = [
            'Grills' => [
                'Kofta', 'Shish Tawook', 'Shish Tawook', 'Kebab',
                 'Shish Tawook',
            ],
            'Appetizers' => [
                'French Fries', 'Spring Rolls', 'Samosa', 'Broasted Wings', 'Fried Eggplant',
                'Mini Pizza', 'Cheese Sticks', 'Stuffed Olives', 'Tahini Dip', 'Moutabal'
            ],
            'Desserts' => [
                'Basbousa', 'Kunafa', 'Balah El Sham', 'Gateau', 'Cheesecake',
                'Tiramisu', 'Mahalabia', 'Rice Pudding', 'Fruit Jelly', 'Custard'
            ],
            'Soups' => [
                'Lentil Soup', 'Cream of Chicken', 'Mushroom Soup', 'Orzo Soup', 'Tomato Soup',
                'Vegetable Soup', 'Seafood Soup', 'Zucchini Soup', 'Chicken Broth', 'Onion Soup'
            ],
            'Fast Food' => [
                'Hot Dog', 'Classic Burger', 'Shawarma Sandwich', 'Crispy Sandwich', 'Zinger Sandwich',
                'Chicken Nuggets', 'Onion Rings', 'Fried Chicken', 'Beef Wrap', 'Fish Fillet Sandwich'
            ],
        ];

        foreach ($dishData as $categoryTitle => $dishes) {
            $category = Category::where('title', $categoryTitle)->first();

            if (!$category) continue;

            foreach ($dishes as $dishTitle) {
                // avoid duplicates if seeder runs more than once
                $imageName = strtolower(str_replace(' ', '_', $dishTitle)) . '.jpg';

                if (CategoryDish::where('title', $dishTitle)->exists()) continue;

                CategoryDish::create([
                    'title' => $dishTitle,
                    'description' => $dishTitle . ' description',
                    'price' => rand(30, 90),
                    'image_url' => asset("assets/$imageName"),
                    'category_id' => $category->id,
                    'availability' => rand(0, 1),
                    'calories' => rand(150, 600),
                ]);
            }
        }
    }
}
