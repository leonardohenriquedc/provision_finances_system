<?php

namespace Database\Seeders;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvisionInstallmentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $provisions = Provision::all();

        if ($provisions->isEmpty()) {
            $this->command->warn('No provisions found. Run ProvisionSeeder first.');

            return;
        }

        $this->command->info('Generating installments for existing provisions...');

        foreach ($provisions as $provision) {
            // Skip if installments already exist for this provision
            if ($provision->provisionInstallments()->count() > 0) {
                $this->command->warn(
                    "Provision ID {$provision->id} already has installments. Skipping."
                );

                continue;
            }

            $baseAmount = $provision->base_amount;
            $installmentCount = $provision->installments;
            $interestRate = $provision->interest_rate ?? 0;
            $interestType = $provision->interest_type;
            $interestPeriod = $provision->interest_period;
            $firstDueDate = $provision->first_due_date;

            // Calculate installment amount
            $rate = $interestRate / 100;
            $monthlyRate = match ($interestPeriod) {
                'DAY' => $rate * 30,
                'YEAR' => $rate / 12,
                default => $rate,
            };

            if ($interestType === 'COMPOUND' && $monthlyRate > 0) {
                $factor = (1 + $monthlyRate) ** $installmentCount;
                $installmentAmount = round(
                    $baseAmount * ($monthlyRate * $factor) / ($factor - 1),
                    2
                );
            } elseif ($monthlyRate > 0) {
                $totalAmount = $baseAmount * (1 + $monthlyRate * $installmentCount);
                $installmentAmount = round($totalAmount / $installmentCount, 2);
            } else {
                $installmentAmount = round($baseAmount / $installmentCount, 2);
            }

            $now = Carbon::now();

            for ($i = 1; $i <= $installmentCount; $i++) {
                $dueDate = Carbon::parse($firstDueDate);

                match ($interestPeriod) {
                    'DAY' => $dueDate->addDays($i - 1),
                    'MONTH' => $dueDate->addMonths($i - 1),
                    'YEAR' => $dueDate->addYears($i - 1),
                    default => $dueDate->addMonths($i - 1),
                };

                // Determine status
                if ($dueDate->isPast()) {
                    $status = $dueDate->diffInDays($now) > 30
                        ? 'LATE'
                        : ($i <= $installmentCount / 2 ? 'PAID' : ($i % 3 === 0 ? 'LATE' : 'LATE_PAYMENT'));
                } else {
                    $status = 'OPEN';
                }

                ProvisionInstallment::create([
                    'provision_id' => $provision->id,
                    'installment_number' => $i,
                    'amount' => $installmentAmount,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'status' => $status,
                ]);
            }

            $this->command->info(
                "Created {$installmentCount} installment(s) for provision ID {$provision->id}."
            );
        }

        $this->command->info('ProvisionInstallmentSeeder completed successfully.');
    }
}
