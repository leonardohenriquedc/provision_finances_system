<?php

namespace Tests\Feature\ProvisionService;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProvisionInstallmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $otherUser;
    private Provision $provision;
    private ProvisionInstallment $installment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->provision = Provision::factory()->create([
            "user_id" => $this->user->id,
            "transaction_type" => "DEBIT",
        ]);

        $this->installment = ProvisionInstallment::create([
            "provision_id" => $this->provision->id,
            "installment_number" => 1,
            "amount" => 150.0,
            "due_date" => now()->addMonth(),
            "status" => "OPEN",
        ]);
    }

    // ──────────────────────────────────────────────
    // index  –  GET installments/{id}
    // ──────────────────────────────────────────────

    public function test_authenticated_user_can_view_installments_of_own_provision(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route("installments", $this->provision->id));

        $response->assertOk();
        $response->assertViewIs("view-installments");
        $response->assertViewHas("provision");
    }

    public function test_unauthenticated_user_cannot_view_installments(): void
    {
        $response = $this->get(route("installments", $this->provision->id));

        $response->assertRedirect(route("login"));
    }

    public function test_user_cannot_view_installments_of_another_users_provision(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route("installments", $this->provision->id));

        $response->assertNotFound();
    }

    public function test_index_returns_404_for_nonexistent_provision(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route("installments", 99999));

        $response->assertNotFound();
    }

    // ──────────────────────────────────────────────
    // view  –  GET installment/{id}
    // ──────────────────────────────────────────────

    public function test_authenticated_user_can_view_own_installment(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route("installment", $this->installment->id));

        $response->assertOk();
        $response->assertViewIs("view-installment");
        $response->assertViewHas("installment");
    }

    public function test_unauthenticated_user_cannot_view_installment(): void
    {
        $response = $this->get(route("installment", $this->installment->id));

        $response->assertRedirect(route("login"));
    }

    public function test_user_cannot_view_another_users_installment(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get(route("installment", $this->installment->id));

        $response->assertNotFound();
    }

    public function test_view_returns_404_for_nonexistent_installment(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route("installment", 99999));

        $response->assertNotFound();
    }

    // ──────────────────────────────────────────────
    // viewCurrentInstallments  –  GET periodinstallments
    // ──────────────────────────────────────────────

    public function test_authenticated_user_can_view_current_period_installments(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route("periodinstallments"));

        $response->assertOk();
        $response->assertViewIs("view-all-installments-per-period");
        $response->assertViewHasAll([
            "installments",
            "month",
            "total",
            "labels",
            "total_month",
            "paid",
            "late",
        ]);
    }

    public function test_unauthenticated_user_cannot_view_period_installments(): void
    {
        $response = $this->get(route("periodinstallments"));

        $response->assertRedirect(route("login"));
    }

    public function test_view_current_installments_filters_by_month(): void
    {
        $this->actingAs($this->user);

        $targetMonth = now()->month;

        $response = $this->get(
            route("periodinstallments", ["month" => $targetMonth]),
        );

        $response->assertOk();
    }

    public function test_view_current_installments_filters_by_year(): void
    {
        $this->actingAs($this->user);

        $currentYear = now()->year;

        $response = $this->get(
            route("periodinstallments", ["year" => $currentYear]),
        );

        $response->assertOk();
    }

    public function test_view_current_installments_filters_by_status(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(
            route("periodinstallments", ["status" => "OPEN"]),
        );

        $response->assertOk();
    }

    public function test_view_current_installments_shows_only_user_own_installments(): void
    {
        $otherProvision = Provision::factory()->create([
            "user_id" => $this->otherUser->id,
        ]);

        ProvisionInstallment::create([
            "provision_id" => $otherProvision->id,
            "installment_number" => 1,
            "amount" => 999.0,
            "due_date" => now()->addMonth(),
            "status" => "OPEN",
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route("periodinstallments"));
        $response->assertOk();

        $installments = $response->viewData("installments");
        $this->assertCount(1, $installments);
        $this->assertEquals(150.0, (float) $installments->first()->amount);
    }

    public function test_view_current_installments_filters_by_transaction_type(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(
            route("periodinstallments", ["transaction_type" => "CREDIT"]),
        );

        $response->assertOk();
    }

    // ──────────────────────────────────────────────
    // updateInstallmentStatus  –  PUT /installment/{id}
    // ──────────────────────────────────────────────

    public function test_authenticated_user_can_update_installment_status(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
        ]);

        $response->assertRedirect(route("installments", $this->provision->id));

        $this->assertDatabaseHas("provisions_installments", [
            "id" => $this->installment->id,
            "status" => "PAID",
        ]);
    }

    public function test_authenticated_user_can_update_installment_status_and_amount(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
            "amount" => "200,50",
        ]);

        $response->assertRedirect(route("installments", $this->provision->id));

        $this->assertDatabaseHas("provisions_installments", [
            "id" => $this->installment->id,
            "status" => "PAID",
            "amount" => 200.5,
        ]);
    }

    public function test_unauthenticated_user_cannot_update_installment(): void
    {
        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
        ]);

        $response->assertRedirect(route("login"));
    }

    public function test_user_cannot_update_another_users_installment(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
        ]);

        $response->assertNotFound();
    }

    public function test_update_installment_returns_404_for_nonexistent_installment(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/99999", [
            "status" => "PAID",
        ]);

        $response->assertNotFound();
    }

    public function test_update_installment_validates_status_is_required(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "",
        ]);

        $response->assertSessionHasErrors("status");
    }

    public function test_update_installment_validates_status_is_valid_enum(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "INVALID_STATUS",
        ]);

        $response->assertSessionHasErrors("status");
    }

    public function test_update_installment_validates_amount_is_numeric(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
            "amount" => "not-a-number",
        ]);

        $response->assertSessionHasErrors("amount");
    }

    public function test_update_installment_can_set_late_payment_status(): void
    {
        $this->actingAs($this->user);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "LATE_PAYMENT",
        ]);

        $response->assertRedirect(route("installments", $this->provision->id));

        $this->assertDatabaseHas("provisions_installments", [
            "id" => $this->installment->id,
            "status" => "LATE_PAYMENT",
        ]);
    }

    public function test_update_installment_preserves_amount_when_not_provided(): void
    {
        $this->actingAs($this->user);

        $this->installment->update(["amount" => 300.0]);

        $response = $this->put("/installment/" . $this->installment->id, [
            "status" => "PAID",
        ]);

        $response->assertRedirect(route("installments", $this->provision->id));

        $this->assertDatabaseHas("provisions_installments", [
            "id" => $this->installment->id,
            "amount" => 300.0,
            "status" => "PAID",
        ]);
    }
}
