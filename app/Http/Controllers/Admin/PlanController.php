<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Plan::class);

        $plans = Plan::withCount('subscriptions')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->paginate(15);

        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        Gate::authorize('create', Plan::class);

        return view('admin.plans.create', ['plan' => new Plan]);
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);

        Plan::create($data);

        return redirect()
            ->route('admin.plans.index')
            ->with('status', __('messages.plan_created'));
    }

    public function edit(Plan $plan): View
    {
        Gate::authorize('update', $plan);

        return view('admin.plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->validated());

        return redirect()
            ->route('admin.plans.index')
            ->with('status', __('messages.plan_updated'));
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        Gate::authorize('delete', $plan);

        $plan->delete();

        return redirect()
            ->route('admin.plans.index')
            ->with('status', __('messages.plan_deleted'));
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'plan';
        $slug = $base;
        $suffix = 1;

        while (Plan::where('slug', $slug)->exists()) {
            $slug = "{$base}-".(++$suffix);
        }

        return $slug;
    }
}
