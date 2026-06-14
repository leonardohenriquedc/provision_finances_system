<?php

namespace App\Http\Controllers;

use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Http\Request;
use League\CommonMark\Delimiter\Bracket;

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

    public function viewCurrentInstallments(Request $request)
    {
        $user = $request->user();

        $year = now()->year;

        $installments = ProvisionInstallment::whereHas('provision', function ($query) use ($user, $request) {
                $query->where('user_id', $user->id);
                if($request->filled("transaction_type")){
                    $query->where('transaction_type', $request->transaction_type);
                }else {
                    $query->where('transaction_type', "DEBIT");
                }
            });

        $month = $request->filled('month');

        if($request->filled('month') && $request->month !== 'todos'){

            try{    
                $month = is_numeric($request->month)
                    ? (int) $request->month
                    : Carbon::createFromLocaleFormat('F', 'pt_BR', $request->month)->month;
    
                    $installments->whereMonth('due_date', $month)
                    ->orderBy('due_date');
            }catch(InvalidFormatException $e ) {
                $month = Carbon::now()->month;

                $month = Carbon::create()
                    ->month($month)
                    ->year($year)
                    ->translatedFormat('F');
            }

        } else {
            $month = "todos";
        }

        if($request->filled('year') && strlen($request->year) === 4){
            try {
                $year = (int) $request->year;

                $installments->whereYear("due_date", $year);
            }catch(InvalidFormatException $e){
                $year = Carbon::now()->year;
            }
        }

        if($request->filled('status')){
            $installments->where('status', $request->status);
        }

        $installments = $installments->get();

        $total = 0;
        $chart = [
            "janeiro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "fevereiro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "março" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "abril" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "maio" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "junho" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "julho" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "agosto" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "setembro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "outubro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "novembro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],

            "dezembro" => [
                "total" => 0,
                "open" => 0,
                "paid" => 0,
                "late" => 0,
            ],
        ];

        foreach($installments as $installment){

            $total += $installment->amount;

            $month_chart = Carbon::parse($installment->due_date)
                ->translatedFormat('F');

            $chart[$month_chart]['total'] += $installment->amount;

            switch($installment->status){
                case 'PAID': 
                    $chart[$month_chart]['paid'] += $installment->amount;
                    break;
                case 'OPEN': 
                    $chart[$month_chart]['open'] += $installment->amount;
                    break;
                case 'LATE': 
                    $chart[$month_chart]['late'] += $installment->amount;
            }
        }

        
        $labels = array_keys($chart);
        $total_month = array_column($chart, 'total');
        $paid = array_column($chart, 'paid');
        $late = array_column($chart, 'late');

        return view('view-all-installments-per-period', compact('installments', 'month', 'total', 'labels', 'total_month', 'paid', 'late'));
    }

    public function updateInstallmentStatus($id, Request $request)
    {
        $user = $request->user();

        $request->merge([
            'amount' => $request->filled('amount')
                ? str_replace(',', '.', $request->amount)
                : null,
        ]);

        $data = $request->validate([
            'status' => 'required|in:OPEN,PAID,LATE,LATE_PAYMENT',
            'amount' => 'nullable|numeric|min:0'
        ]);

        $installment = ProvisionInstallment::whereHas('provision', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $installment->update([
            'status' => $data['status']
        ]);

        if($data['amount']){
            $installment->update([
                'amount' => $data['amount']
                ]);
        }


        return redirect()->route('installments', $installment->provision->id);
    }
}
