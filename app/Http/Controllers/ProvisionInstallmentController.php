<?php

namespace App\Http\Controllers;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Illuminate\Http\Request;

class ProvisionInstallmentController extends Controller
{

    public function index($id, Request $request)
    {
        $provision = Provision::with('provisionInstallments')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return view('view-installments', compact('provision'));
    }

    public function view($id, Request $request)
    {
        $installment = ProvisionInstallment::with('provision')
            ->whereHas('provision', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->findOrFail($id);

        return view('view-installment', compact('installment'));
    }

    public function updateInstallmentStatus($id, Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'status' => 'required|in:OPEN,PAID,LATE',
        ]);

        // busca a parcela garantindo que pertence ao usuário
        $installment = ProvisionInstallment::whereHas('provision', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        // atualização
        $installment->update([
            'status' => $data['status']
        ]);

        return redirect()->route('installments', $installment->provision->id);
    }
}
