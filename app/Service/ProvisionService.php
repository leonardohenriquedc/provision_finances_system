<?php

namespace App\Service;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvisionService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function create(Request $request)
    {
        $user = $request->user();

        $request->merge([
            "base_amount" => str_replace(",", ".", $request->base_amount),
        ]);

        $data = $request->validate([
            "transaction_type" => "required|in:DEBIT,CREDIT",
            "description" => "required|string|max:255",
            "base_amount" => "required|string|min:0",
            "interest_rate" => "nullable|numeric|min:0",
            "interest_type" => "nullable|in:SIMPLE,COMPOUND",
            "interest_period" => "nullable|in:DAY,MONTH,YEAR",
            "installments" => "required|integer|min:1",
            "competence_date" => "required|date",
            "first_due_date" => "required|date",
        ]);

        $data["user_id"] = $user->id;

        $status = DB::transaction(function () use ($data) {
            // 1. Criar provision
            $provision = Provision::create($data);

            $baseAmount = $data["base_amount"];
            $rate = $data["interest_rate"] ?? 0;
            $installments = $data["installments"];

            $firstDueDate = Carbon::parse($data["first_due_date"]);

            // 2. Converter taxa (% → decimal)
            $rate = $rate / 100;

            for ($i = 1; $i <= $installments; $i++) {
                // tempo (t)
                switch ($data["interest_period"] ?? "MONTH") {
                    case "DAY":
                        $t = $i; // dias
                        break;

                    case "YEAR":
                        $t = $i; // anos
                        break;

                    case "MONTH":
                    default:
                        $t = $i; // meses
                        break;
                }

                // ajuste conforme base
                if ($data["interest_period"] === "YEAR") {
                    $t = $i / 12;
                } elseif ($data["interest_period"] === "DAY") {
                    $t = $i * 30; // simplificação
                }

                // 3. cálculo do valor
                if ($rate > 0 && $data["interest_type"]) {
                    if ($data["interest_type"] === "SIMPLE") {
                        $amount = $baseAmount * (1 + $rate * $t);
                    } else {
                        $amount = $baseAmount * pow(1 + $rate, $t);
                    }
                } else {
                    $amount = $baseAmount;
                }

                // 4. data da parcela
                switch ($data["interest_period"] ?? "MONTH") {
                    case "DAY":
                        $dueDate = $firstDueDate->copy()->addDays($i - 1);
                        break;

                    case "YEAR":
                        $dueDate = $firstDueDate->copy()->addYears($i - 1);
                        break;

                    case "MONTH":
                    default:
                        $dueDate = $firstDueDate->copy()->addMonths($i - 1);
                        break;
                }

                $status = "OPEN";

                if ($dueDate < Carbon::parse(time())) {
                    $status = "LATE";
                }

                // 5. criar parcela
                ProvisionInstallment::create([
                    "provision_id" => $provision->id,
                    "installment_number" => $i,
                    "amount" => round($amount, 2),
                    "due_date" => $dueDate,
                    "status" => $status,
                ]);
            }

            return true;
        });

        if ($status) {
            return 201;
        } else {
            return 400;
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $provision = Provision::with("provisionInstallments")
            ->where("user_id", $user->id)
            ->findOrFail($id);

        $data = $request->validate([
            "transaction_type" => "required|in:DEBIT,CREDIT",
            "description" => "required|string|max:255",
            "base_amount" => "required|numeric|min:0",
            "interest_rate" => "nullable|numeric|min:0",
            "interest_type" => "nullable|in:SIMPLE,COMPOUND",
            "interest_period" => "nullable|in:DAY,MONTH,YEAR",
            "installments" => "required|integer|min:1",
            "competence_date" => "required|date",
            "first_due_date" => "required|date",
        ]);

        $status = DB::transaction(function () use ($data, $provision) {
            // 1. Atualiza provision
            $provision->update($data);

            // 2. Remove parcelas antigas
            $provision->provisionInstallments()->delete();

            $baseAmount = $data["base_amount"];
            $rate = ($data["interest_rate"] ?? 0) / 100;
            $installments = $data["installments"];
            $firstDueDate = Carbon::parse($data["first_due_date"]);

            // 3. Recria parcelas
            for ($i = 1; $i <= $installments; $i++) {
                // tempo (t)
                $t = $i;

                if (($data["interest_period"] ?? null) === "YEAR") {
                    $t = $i / 12;
                } elseif (($data["interest_period"] ?? null) === "DAY") {
                    $t = $i * 30;
                }

                // cálculo
                if ($rate > 0 && ($data["interest_type"] ?? null)) {
                    if ($data["interest_type"] === "SIMPLE") {
                        $amount = $baseAmount * (1 + $rate * $t);
                    } else {
                        $amount = $baseAmount * pow(1 + $rate, $t);
                    }
                } else {
                    $amount = $baseAmount;
                }

                $dueDate = $firstDueDate->copy()->addMonths($i - 1);

                ProvisionInstallment::create([
                    "provision_id" => $provision->id,
                    "installment_number" => $i,
                    "amount" => round($amount, 2),
                    "due_date" => $dueDate,
                    "status" => "OPEN",
                ]);
            }

            return true;
        });

        if ($status) {
            return 201;
        } else {
            return 400;
        }
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();

        $provision = Provision::where("user_id", $user->id)->findOrFail($id);

        $provision->delete();

        return 204;
    }
}
