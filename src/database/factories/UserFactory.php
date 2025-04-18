<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_admin' => false,
            'status_id' => 1,
        ];
    }

    public function admin()
    {
        return $this->state(fn () => [
            'is_admin' => true,
        ]);
    }

    public function general()
    {
        return $this->state(fn () => [
            'is_admin' => false,
        ]);
    }

    public function unverified()
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
