<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProvisionCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_create_provision(){
        $user = User::factory()->create();

        $data = [
            'transaction_type' => 'DEBIT',
            'description' => 'Compra de equipamentos de informática',
            'base_amount' => '1500.00',
            'interest_rate' => 2.5,
            'interest_type' => 'COMPOUND',
            'interest_period' => 'MONTH',
            'installments' => 12,
            'competence_date' => '2026-06-01',
            'first_due_date' => '2026-07-10',
        ];

        $response = $this->actingAs($user)->post("/provision", $data);

        $response->assertRedirect("/dashboard");
    }
}
