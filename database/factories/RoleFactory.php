<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string,mixed>
     */
    public function definition()
    {
        // Flatten available permissions from Role model
        $available = collect(Role::getAvailablePermissions())
            ->map(fn($group) => array_keys($group))
            ->collapse()
            ->unique()
            ->values()
            ->toArray();

        $permissions = [];
        if (!empty($available)) {
            $permissions = $this->faker->randomElements($available, $this->faker->numberBetween(1, min(6, count($available))));
        }

        return [
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->sentence(),
            'permissions' => $permissions,
            // Default roles should be active in tests to avoid
            // random permission failures caused by faker booleans.
            'is_active' => true,
            'is_default' => false,
            'priority' => $this->faker->numberBetween(1, 10),
        ];
    }
}
