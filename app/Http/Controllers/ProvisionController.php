<?php

namespace App\Http\Controllers;

use App\Service\ProvisionService;
use Illuminate\Http\Request;
use App\Models\Provision;

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
        $service = new ProvisionService();

        $datas = $service->index($request);

        if (!is_null($datas)) {
            [
                $provisions,
                $chartValues,
                $total,
                $paid,
                $pending,
                $month,
            ] = $datas;

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
        } else {
            return view("error");
        }
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
