<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTestRequest;
use App\Models\Group;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestController extends Controller
{
    public function index(): View
    {
        $tests = Test::with('group')->withCount(['questions', 'attempts'])->latest()->paginate(15);

        return view('tenant.tests.index', compact('tests'));
    }

    public function create(): View
    {
        return view('tenant.tests.create', [
            'test' => new Test,
            'groups' => Group::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTestRequest $request): RedirectResponse
    {
        $test = Test::create($request->validated());

        return redirect()->route('tenant.tests.show', $test)->with('status', __('messages.test_saved'));
    }

    public function show(Test $test): View
    {
        $test->load(['group', 'questions.options']);
        $attempts = $test->attempts()->with('student')->whereNotNull('submitted_at')->latest('submitted_at')->get();

        return view('tenant.tests.show', compact('test', 'attempts'));
    }

    public function edit(Test $test): View
    {
        return view('tenant.tests.edit', [
            'test' => $test,
            'groups' => Group::active()->orderBy('name')->get(),
        ]);
    }

    public function update(StoreTestRequest $request, Test $test): RedirectResponse
    {
        $test->update($request->validated());

        return redirect()->route('tenant.tests.show', $test)->with('status', __('messages.test_saved'));
    }

    public function destroy(Test $test): RedirectResponse
    {
        $test->delete();

        return redirect()->route('tenant.tests.index')->with('status', __('messages.test_deleted'));
    }

    /**
     * Publish / unpublish a test. Publishing requires at least one question.
     */
    public function togglePublish(Test $test): RedirectResponse
    {
        if (! $test->is_published && $test->questions()->doesntExist()) {
            return back()->withErrors(['publish' => __('tests.need_questions')]);
        }

        $test->update(['is_published' => ! $test->is_published]);

        return back()->with('status', __('messages.test_saved'));
    }
}
