<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreChargeRequest;
use App\Models\Charge;
use Illuminate\Http\RedirectResponse;

class ChargeController extends Controller
{
    public function store(StoreChargeRequest $request): RedirectResponse
    {
        $charge = Charge::create($request->validated());

        return redirect()
            ->route('tenant.students.show', $charge->student_id)
            ->with('status', __('messages.charge_saved'));
    }

    public function destroy(Charge $charge): RedirectResponse
    {
        $studentId = $charge->student_id;
        $charge->delete();

        return redirect()
            ->route('tenant.students.show', $studentId)
            ->with('status', __('messages.charge_deleted'));
    }
}
