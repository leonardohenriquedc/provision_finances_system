<?php

namespace Tests\Feature\ProvisionService;

use App\Models\Provision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProvisionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_update_provision()
    {
        $user = User::factory()->create();

        $provision = Provision::factory()->create([
            "user_id" => $user->id,
        ]);

        $provisionData = [
            "id" => 1,
            "description" => 'New loan with a base amount of $5,000.',
            "user_id" => $user->id, // User ID of the borrower
            "base_amount" => 5000.0,
            "interest_rate" => 0.05, // 5% interest rate
            "interest_type" => "",
            "interest_period" => "MONTH",
            "installments" => random_int(1, 12),
            "competence_date" => now()->subMonths(6)->format("Y-m-d"),
            "first_due_date" => now()->addYears(1)->format("Y-m-d"),
            "transaction_type" => "DEBIT",
        ];

        $response = $this->actingAs($user)->put(
            "/provision/" . $provision->id,
            $provisionData,
        );

        $provision = Provision::find($provision->id);

        $response->assertStatus(302);
        $response->assertRedirect("/dashboard");
    }

    public function test_create_provision()
    {
        $user = User::factory()->create();

        $provisionData = [
            "description" => 'New loan with a base amount of $5,000.',
            "user_id" => $user->id, // User ID of the borrower
            "base_amount" => 5000.0,
            "interest_rate" => 0.05, // 5% interest rate
            "interest_type" => "",
            "interest_period" => "MONTH",
            "installments" => random_int(1, 12),
            "competence_date" => now()->subMonths(6)->format("Y-m-d"),
            "first_due_date" => now()->addYears(1)->format("Y-m-d"),
            "transaction_type" => "CREDIT",
        ];

        $response = $this->actingAs($user)->post("/provision", $provisionData);
        $response->assertStatus(302);
        $response->assertRedirect("/dashboard");
    }

    public function test_delete_provision()
    {
        $user = User::factory()->create();

        $provision = Provision::factory()->create(["user_id" => $user->id]);

        $response = $this->actingAs($user)->delete(
            "/provision/" . $provision->id,
        );

        $response->assertStatus(302);
        $response->assertRedirect("/provisions");

        $this->assertDatabaseMissing("provisions", ["id" => $provision->id]);
    }
}
