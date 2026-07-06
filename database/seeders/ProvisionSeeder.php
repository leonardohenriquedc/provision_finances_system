<?php

namespace Database\Seeders;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvisionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Fetch all users to distribute provisions among them
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Run UserSeeder first.');

            return;
        }

        $provisionsData = [
            [
                'description' => 'Financiamento Imobiliário - Apartamento 123',
                'base_amount' => 150000.00,
                'interest_rate' => 8.50,
                'interest_type' => 'COMPOUND',
                'interest_period' => 'MONTH',
                'installments' => 12,
                'competence_date' => '2026-01-01',
                'first_due_date' => '2026-02-01',
                'transaction_type' => 'DEBIT',
            ],
            [
                'description' => 'Empréstimo Pessoal - Reforma',
                'base_amount' => 8000.00,
                'interest_rate' => 3.75,
                'interest_type' => 'SIMPLE',
                'interest_period' => 'MONTH',
                'installments' => 6,
                'competence_date' => '2026-03-15',
                'first_due_date' => '2026-04-15',
                'transaction_type' => 'DEBIT',
            ],
            [
                'description' => 'Investimento em CDB',
                'base_amount' => 25000.00,
                'interest_rate' => 13.50,
                'interest_type' => 'COMPOUND',
                'interest_period' => 'YEAR',
                'installments' => 24,
                'competence_date' => '2025-06-01',
                'first_due_date' => '2025-07-01',
                'transaction_type' => 'CREDIT',
            ],
            [
                'description' => 'Compra de Veículo',
                'base_amount' => 45000.00,
                'interest_rate' => 6.00,
                'interest_type' => 'SIMPLE',
                'interest_period' => 'MONTH',
                'installments' => 8,
                'competence_date' => '2026-05-10',
                'first_due_date' => '2026-06-10',
                'transaction_type' => 'DEBIT',
            ],
            [
                'description' => 'Curso de Especialização',
                'base_amount' => 3200.00,
                'interest_rate' => 2.50,
                'interest_type' => 'SIMPLE',
                'interest_period' => 'MONTH',
                'installments' => 3,
                'competence_date' => '2026-06-01',
                'first_due_date' => '2026-07-01',
                'transaction_type' => 'DEBIT',
            ],
            [
                'description' => 'Financiamento de Equipamentos',
                'base_amount' => 12000.00,
                'interest_rate' => 5.00,
                'interest_type' => 'COMPOUND',
                'interest_period' => 'MONTH',
                'installments' => 10,
                'competence_date' => '2026-02-20',
                'first_due_date' => '2026-03-20',
                'transaction_type' => 'DEBIT',
            ],
            [
                'description' => 'Aplicação em Tesouro Direto',
                'base_amount' => 50000.00,
                'interest_rate' => 12.00,
                'interest_type' => 'COMPOUND',
                'interest_period' => 'YEAR',
                'installments' => 36,
                'competence_date' => '2024-01-01',
                'first_due_date' => '2024-02-01',
                'transaction_type' => 'CREDIT',
            ],
            [
                'description' => 'Parcelamento de Dívida Cartão',
                'base_amount' => 5600.00,
                'interest_rate' => 9.00,
                'interest_type' => 'COMPOUND',
                'interest_period' => 'MONTH',
                'installments' => 5,
                'competence_date' => '2026-04-05',
                'first_due_date' => '2026-05-05',
                'transaction_type' => 'DEBIT',
            ],
        ];

        foreach ($provisionsData as $index => $data) {
            $user = $users->get($index % $users->count());

            $provision = Provision::create(array_merge($data, [
                'user_id' => $user->id,
            ]));

            $this->createInstallments($provision, $data);

            $this->command->info("Created provision '{$data['description']}' with {$data['installments']} installment(s).");
        }

        // Create some extra random provisions using the factory
        Provision::factory()
            ->count(10)
            ->make()
            ->each(function (Provision $provision) use ($users) {
                $provision->user_id = $users->random()->id;
                $provision->save();

                $this->createInstallments($provision, $provision->toArray());
            });

        $this->command->info('ProvisionSeeder completed successfully.');
    }

    /**
     * Create installments for a given provision.
     */
    private function createInstallments(Provision $provision, array $data): void
    {
        $installments = $data['installments'] ?? $provision->installments;
        $baseAmount = $data['base_amount'] ?? $provision->base_amount;
        $interestRate = $data['interest_rate'] ?? $provision->interest_rate ?? 0;
        $interestType = $data['interest_type'] ?? $provision->interest_type;
        $interestPeriod = $data['interest_period'] ?? $provision->interest_period;
        $firstDueDate = $data['first_due_date'] ?? $provision->first_due_date;

        // Calculate installment amount with interest
        $installmentAmount = $this->calculateInstallmentAmount(
            $baseAmount,
            $interestRate,
            $interestType,
            $interestPeriod,
            $installments
        );

        for ($i = 1; $i <= $installments; $i++) {
            $dueDate = Carbon::parse($firstDueDate);

            // Increment due date based on interest period
            match ($interestPeriod) {
                'DAY' => $dueDate->addDays($i - 1),
                'MONTH' => $dueDate->addMonths($i - 1),
                'YEAR' => $dueDate->addYears($i - 1),
                default => $dueDate->addMonths($i - 1),
            };

            // Determine a realistic status
            $status = $this->determineInstallmentStatus($dueDate, $i, $installments);

            ProvisionInstallment::create([
                'provision_id' => $provision->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => $status,
            ]);
        }
    }

    /**
     * Calculate the installment amount based on interest type.
     */
    private function calculateInstallmentAmount(
        float $baseAmount,
        float $interestRate,
        string $interestType,
        string $interestPeriod,
        int $installments
    ): float {
        $rate = $interestRate / 100; // Convert percentage to decimal

        if ($rate <= 0 || $installments <= 0) {
            return round($baseAmount / max($installments, 1), 2);
        }

        // Normalize rate to monthly if needed (simplified approach)
        $monthlyRate = match ($interestPeriod) {
            'DAY' => $rate * 30, // Approximate daily to monthly
            'YEAR' => $rate / 12, // Convert yearly to monthly
            default => $rate, // Already monthly
        };

        if ($interestType === 'COMPOUND') {
            // PMT formula for compound interest
            // PMT = PV * [r * (1 + r)^n] / [(1 + r)^n - 1]
            $factor = (1 + $monthlyRate) ** $installments;

            return round($baseAmount * ($monthlyRate * $factor) / ($factor - 1), 2);
        }

        // Simple interest: total = principal * (1 + rate * time), then divide by installments
        $totalAmount = $baseAmount * (1 + $monthlyRate * $installments);

        return round($totalAmount / $installments, 2);
    }

    /**
     * Determine a realistic installment status based on due date.
     */
    private function determineInstallmentStatus(Carbon $dueDate, int $currentInstallment, int $totalInstallments): string
    {
        $now = Carbon::now();

        if ($dueDate->isPast()) {
            if ($dueDate->diffInDays($now) > 30) {
                return 'LATE';
            }

            // Mix some PAID and LATE for past installments
            if ($currentInstallment <= $totalInstallments / 2) {
                return 'PAID';
            }

            return $currentInstallment % 3 === 0 ? 'LATE' : 'LATE_PAYMENT';
        }

        return 'OPEN';
    }
}
