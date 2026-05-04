<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PHPUnit\Logging\OpenTestReporting\Status;

class ProvisionController extends Controller
{

    public function viewCreate(){
        return view('create-provision');
    }

    public function edit($id, Request $request)
    {
        $provision = Provision::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return view('view-provision', compact('provision'));
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Provision::with('provisionInstallments')
            ->where('user_id', $user->id);

        // Filtro por mês
        if ($request->filled('month')) {
            $query->whereMonth('competence_date', $request->month);
        }

        $provisions = $query->get();

        // KPIs
        $total = 0;
        $paid = 0;
        $pending = 0;

        foreach ($provisions as $provision) {
            $installments = $provision->provisionInstallments;

            $sum = $installments->sum('amount') ?: $provision->base_amount;

            $total += $sum;

            foreach ($installments as $i) {
                if ($i->status === 'PAID') {
                    $paid += $i->amount;
                } else {
                    $pending += $i->amount;
                }
            }
        }

        return view('dashboard', compact(
            'provisions',
            'total',
            'paid',
            'pending'
        ));
    }
    public function create(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'interest_type' => 'nullable|in:SIMPLE,COMPOUND',
            'interest_period' => 'nullable|in:DAY,MONTH,YEAR',
            'installments' => 'required|integer|min:1',
            'competence_date' => 'required|date',
            'first_due_date' => 'required|date',
        ]);

        $data['user_id'] = $user->id;

        return DB::transaction(function () use ($data) {

            // 1. Criar provision
            $provision = Provision::create($data);

            $baseAmount = $data['base_amount'];
            $rate = $data['interest_rate'] ?? 0;
            $installments = $data['installments'];

            $firstDueDate = Carbon::parse($data['first_due_date']);

            // 2. Converter taxa (% → decimal)
            $rate = $rate / 100;

            for ($i = 1; $i <= $installments; $i++) {

                    // tempo (t)
                switch ($data['interest_period'] ?? 'MONTH') {

                    case 'DAY':
                        $t = $i; // dias
                        break;

                    case 'YEAR':
                        $t = $i; // anos
                        break;

                    case 'MONTH':
                    default:
                        $t = $i; // meses
                        break;
                }

                // ajuste conforme base
                if ($data['interest_period'] === 'YEAR') {
                    $t = $i / 12;
                } elseif ($data['interest_period'] === 'DAY') {
                    $t = $i * 30; // simplificação
                }

                // 3. cálculo do valor
                if ($rate > 0 && $data['interest_type']) {

                    if ($data['interest_type'] === 'SIMPLE') {
                        $amount = $baseAmount * (1 + ($rate * $t));
                    } else {
                        $amount = $baseAmount * pow((1 + $rate), $t);
                    }

                } else {
                    $amount = $baseAmount;
                }

                // 4. data da parcela
                switch ($data['interest_period'] ?? 'MONTH') {

                    case 'DAY':
                        $dueDate = $firstDueDate->copy()->addDays($i - 1);
                        break;

                    case 'YEAR':
                        $dueDate = $firstDueDate->copy()->addYears($i - 1);
                        break;

                    case 'MONTH':
                    default:
                        $dueDate = $firstDueDate->copy()->addMonths($i - 1);
                        break;
                }

                $status = 'OPEN';

                if ($dueDate < Carbon::parse(time())){
                    $status = 'LATE';
                }

                // 5. criar parcela
                ProvisionInstallment::create([
                    'provision_id' => $provision->id,
                    'installment_number' => $i,
                    'amount' => round($amount, 2),
                    'due_date' => $dueDate,
                    'status' => $status,
                ]);
            }

            return redirect()->route('dashboard');
        });
    }

    public function update($id, Request $request)
    {
        $user = $request->user();

        $provision = Provision::with('provisionInstallments')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'interest_type' => 'nullable|in:SIMPLE,COMPOUND',
            'interest_period' => 'nullable|in:DAY,MONTH,YEAR',
            'installments' => 'required|integer|min:1',
            'competence_date' => 'required|date',
            'first_due_date' => 'required|date',
        ]);

        return DB::transaction(function () use ($data, $provision) {

            // 1. Atualiza provision
            $provision->update($data);

            // 2. Remove parcelas antigas
            $provision->provisionInstallments()->delete();

            $baseAmount = $data['base_amount'];
            $rate = ($data['interest_rate'] ?? 0) / 100;
            $installments = $data['installments'];
            $firstDueDate = Carbon::parse($data['first_due_date']);

            // 3. Recria parcelas
            for ($i = 1; $i <= $installments; $i++) {

                // tempo (t)
                $t = $i;

                if (($data['interest_period'] ?? null) === 'YEAR') {
                    $t = $i / 12;
                } elseif (($data['interest_period'] ?? null) === 'DAY') {
                    $t = $i * 30;
                }

                // cálculo
                if ($rate > 0 && ($data['interest_type'] ?? null)) {

                    if ($data['interest_type'] === 'SIMPLE') {
                        $amount = $baseAmount * (1 + ($rate * $t));
                    } else {
                        $amount = $baseAmount * pow((1 + $rate), $t);
                    }

                } else {
                    $amount = $baseAmount;
                }

                $dueDate = $firstDueDate->copy()->addMonths($i - 1);

                ProvisionInstallment::create([
                    'provision_id' => $provision->id,
                    'installment_number' => $i,
                    'amount' => round($amount, 2),
                    'due_date' => $dueDate,
                    'status' => 'OPEN',
                ]);
            }

            return redirect()->route('dashboard');
        });
    }

    public function delete($id, Request $request)
    {
        $user = $request->user();

        // garante que o provision pertence ao usuário
        $provision = Provision::where('user_id', $user->id)
            ->findOrFail($id);

        // delete (cascade remove as parcelas automaticamente)
        $provision->delete();

        return redirect('/provisions')
            ->with('success', 'Provisionamento excluído com sucesso');
    }
}
