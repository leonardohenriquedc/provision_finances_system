<?php

namespace App\Http\Controllers;

use App\Service\ProvisionInstallmentService;
use Illuminate\Http\Request;

class ProvisionInstallmentController extends Controller
{
    public function index($id, Request $request)
    {
        $service = new ProvisionInstallmentService();

        $provision = $service->index($id, $request);

        return view("view-installments", compact("provision"));
    }

    public function view($id, Request $request)
    {
        $service = new ProvisionInstallmentService();

        $installment = $service->view($id, $request);

        return view("view-installment", compact("installment"));
    }

    public function viewCurrentInstallments(Request $request)
    {
        $service = new ProvisionInstallmentService();

        $datas = $service->viewCurrentInstallments($request);

        if (!is_null($datas)) {
            [
                $installments,
                $month,
                $total,
                $labels,
                $totalMonth,
                $paid,
                $late,
            ] = $datas;

            return view(
                "view-all-installments-per-period",
                compact(
                    "installments",
                    "month",
                    "total",
                    "labels",
                    "paid",
                    "late",
                ) + ["total_month" => $totalMonth],
            );
        }

        return view("error");
    }

    public function updateInstallmentStatus($id, Request $request)
    {
        $service = new ProvisionInstallmentService();

        $provisionId = $service->updateInstallmentStatus($id, $request);

        if ($provisionId) {
            return redirect()->route("installments", $provisionId);
        }

        return redirect()
            ->route("error")
            ->with("message", "Parcela não foi atualizada com sucesso.");
    }
}
