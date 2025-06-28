<?php

namespace Database\Factories;
use Carbon\Carbon;

use App\Models\{
    User,
    Customer,
    Staff
};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'fname' => $this->faker->firstName,
            'lname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->unique()->numerify('010########'),
            // 'role' => $this->faker->randomElement(['admin', 'kitchen_staff', 'customer', 'delivery']),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default: "password"
            'remember_token' => Str::random(10),
        ];
    }

     public function withRole(string $role): static
    {
        return $this->state(fn () => ['role' => $role]);
    }


    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if ($user->role === 'customer') {
                Customer::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
            if (in_array($user->role, ['kitchen_staff', 'delivery'])) {
                Staff::factory()->create([
                    'user_id' => $user->id,
                    'shift_start' => $start = Carbon::createFromTime(rand(0, 23), rand(0, 59), 0)->format('H:i:s'),
                    'shift_end' => Carbon::parse($start)->addHours(8)->format('H:i:s'),
                ]);
            }
        });
    }

    /**
     * State for a specific role: customer
     */
    // public function customer(): static
    // {
    //     return $this->state(fn () => ['role' => 'customer']);
    // }

    // /**
    //  * State for kitchen staff
    //  */
    // public function kitchenStaff(): static
    // {
    //     return $this->state(fn () => ['role' => 'kitchen_staff']);
    // }

    // /**
    //  * State for delivery
    //  */
    // public function delivery(): static
    // {
    //     return $this->state(fn () => ['role' => 'delivery']);
    // }

    // /**
    //  * State for admin
    //  */
    // public function admin(): static
    // {
    //     return $this->state(fn () => ['role' => 'admin']);
    // }

   
}
