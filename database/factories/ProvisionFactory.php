<?php

namespace Database\Factories;

use App\Models\Provision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Provision>
 */
class ProvisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "description" => $this->faker->sentence(3),
            "user_id" => $this->faker->numberBetween(1, 50), // Assuming a user ID between 1 and 50
            "base_amount" => $this->faker->randomFloat(2, 100, 1000),
            "interest_rate" => $this->faker->randomFloat(4, 3.0, 5.0), // Interest rate in decimal format
            "interest_type" => $this->faker->randomElement([
                "SIMPLE",
                "COMPOUND",
            ]),
            "interest_period" => $this->faker->randomElement([
                "DAY",
                "MONTH",
                "YEAR",
            ]), // Months
            "installments" => $this->faker->numberBetween(6, 30), // Number of installments
            "competence_date" => now()->subMonths(rand(1, 12)),
            "first_due_date" => now()->addMonths(rand(1, 12)),
            "transaction_type" => $this->faker->randomElement([
                "DEBIT",
                "CREDIT",
            ]),
        ];
    }
}
