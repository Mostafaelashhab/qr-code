<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Quick search across the tenant's students and groups.
     */
    public function __invoke(Request $request): View
    {
        $query = $request->string('q')->trim()->toString();

        $students = collect();
        $groups = collect();

        if ($query !== '') {
            $students = Student::query()
                ->where(fn ($q) => $q->where('name', 'like', "%{$query}%")->orWhere('phone', 'like', "%{$query}%"))
                ->orderBy('name')
                ->limit(15)
                ->get();

            $groups = Group::with('subject')
                ->where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        return view('tenant.search', compact('query', 'students', 'groups'));
    }
}
