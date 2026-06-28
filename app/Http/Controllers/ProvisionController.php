<?php

namespace App\Http\Controllers;

use App\Service\ProvisionService;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use App\Models\Provision;
use App\Models\ProvisionInstallment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PHPUnit\Logging\OpenTestReporting\Status;

class ProvisionController extends Controller
{
    public function viewCreate()
    {
        return view("create-provision");
    }

    public function edit($id, Request $request)
    {
        $provision = Provision::where(
            "user_id",
            $request->user()->id,
        )->findOrFail($id);

        return view("view-provision", compact("provision"));
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Provision::with("provisionInstallments")->where(
            "user_id",
            $user->id,
        );

        $month = "";

        if ($request->filled("month") && $request->month !== "todos") {
            try {
                $month = is_numeric($request->month)
                    ? (int) $request->month
                    : Carbon::createFromLocaleFormat(
                        "F",
                        "pt_BR",
                        $request->month,
                    )->month;

                $query->whereMonth("competence_date", $month);
            } catch (InvalidFormatException $e) {
                $month = Carbon::now()->month;

                $month = Carbon::create()->month($month)->translatedFormat("F");
            }
        } else {
            $month = "todos";
        }

        if ($request->filled("year") && strlen($request->year) === 4) {
            try {
                $year = (int) $request->year;

                $query->whereYear("competence_date", $year);
            } catch (InvalidFormatException $e) {
                $year = Carbon::now()->year;
            }
        }

        $provisions = $query->get();

        // KPIs
        $total = 0;
        $paid = 0;
        $pending = 0;

        $chartValues = [];

        foreach ($provisions as $provision) {
            $installments = $provision->provisionInstallments;

            $sum = $installments->sum("amount") ?: $provision->base_amount;

            if (!array_key_exists($provision->competence_date, $chartValues)) {
                $chartValues[$provision->competence_date] = 0;
            }

            $chartValues[$provision->competence_date] += $sum;

            $total += $sum;

            foreach ($installments as $i) {
                if ($i->status === "PAID") {
                    $paid += $i->amount;
                } else {
                    $pending += $i->amount;
                }
            }
        }

        return view(
            "dashboard",
            compact(
                "provisions",
                "chartValues",
                "total",
                "paid",
                "pending",
                "month",
            ),
        );
    }
    public function create(Request $request)
    {
        $service = new ProvisionService();

        $status = $service->create($request);

        if ($status === 201) {
            return redirect()->route("dashboard");
        } else {
            return redirect()
                ->route("error")
                ->with("message", "Provision não foi criado com sucesso.");
        }
    }

    public function update($id, Request $request)
    {
        $service = new ProvisionService();

        $status = $service->update($request, $id);
        error_log($status);
        if ($status === 201) {
            return redirect()->route("dashboard");
        } else {
            return redirect()
                ->route("error")
                ->with("message", "Provision não foi atualizado com sucesso.");
        }
    }

    public function delete($id, Request $request)
    {
        $service = new ProvisionService();

        $status = $service->delete($request, $id);

        if ($status === 204) {
            return redirect()->route("provisions");
        } else {
            return redirect()
                ->route("error")
                ->with("message", "Provision não foi deletado com sucesso.");
        }
    }
}
