<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * User roles available in the application.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_USER = 'user';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => self::ROLE_USER,
            'created_at' => now(),
            'updated_at' => now(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set a specific password for the user.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Set the user as an admin.
     */
    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => self::ROLE_ADMIN,
        ]);
    }

    /**
     * Set the user as a manager.
     */
    public function asManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => self::ROLE_MANAGER,
        ]);
    }

    /**
     * Add profile information to the user.
     */
    public function withProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'bio' => fake()->paragraph(2),
        ]);
    }

    /**
     * Set the user as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Set custom creation date.
     */
    public function createdAt(\DateTime $date): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}