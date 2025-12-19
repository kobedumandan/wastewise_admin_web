<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_username' => fake()->unique()->userName(),
            'admin_password' => Hash::make('password'), // Default password: 'password'
            'admin_fname' => fake()->firstName(),
            'admin_lname' => fake()->lastName(),
            'admin_cell' => fake()->numerify('09#########'),
        ];
    }

    /**
     * Create admin with specific username
     */
    public function withUsername(string $username): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_username' => $username,
        ]);
    }

    /**
     * Create admin with specific password
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_password' => Hash::make($password),
        ]);
    }

    /**
     * Create admin with specific details
     */
    public function withDetails(string $firstName, string $lastName, string $phone = null): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_fname' => $firstName,
            'admin_lname' => $lastName,
            'admin_cell' => $phone ?? fake()->numerify('09#########'),
        ]);
    }

    /**
     * Override create to work with Firebase
     */
    public function create($attributes = [], $parent = null)
    {
        $attributes = array_merge($this->definition(), $attributes);
        
        // Create in Firebase
        $admin = new Admin($attributes);
        $admin->save();
        
        return $admin;
    }

    /**
     * Create multiple admins
     */
    public function count(int $count)
    {
        $admins = [];
        
        for ($i = 0; $i < $count; $i++) {
            $admins[] = $this->create();
        }
        
        return collect($admins);
    }
}
