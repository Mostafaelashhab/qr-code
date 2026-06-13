<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreStudentRequest;
use App\Http\Requests\Tenant\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::query()
            ->withCount('enrollments')
            ->withSum('payments as paid_total', 'amount')
            ->withSum('charges as charged_total', 'amount')
            ->withSum('charges as discount_total', 'discount')
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('tenant.students.create', ['student' => new Student]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        if (! $request->user()->client->canAddStudent()) {
            return back()->withInput()->withErrors(['name' => __('messages.plan_student_limit')]);
        }

        Student::create($request->validated());

        return redirect()->route('tenant.students.index')->with('status', __('messages.student_saved'));
    }

    public function show(Student $student): View
    {
        $student->load(['groups.subject', 'payments.group', 'charges.group']);

        return view('tenant.students.show', [
            'student' => $student,
            'groups' => \App\Models\Group::active()->orderBy('name')->get(),
            'totalCharged' => $student->totalCharged(),
            'totalPaid' => $student->totalPaid(),
            'balance' => $student->balance(),
        ]);
    }

    public function edit(Student $student): View
    {
        return view('tenant.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()->route('tenant.students.index')->with('status', __('messages.student_saved'));
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('tenant.students.index')->with('status', __('messages.student_deleted'));
    }

    /**
     * Printable QR cards for all active students (used for QR attendance check-in).
     */
    public function qrCards(): View
    {
        $students = Student::active()->orderBy('name')->get();

        return view('tenant.students.cards', compact('students'));
    }

    /**
     * Printable QR card for a single student.
     */
    public function card(Student $student): View
    {
        return view('tenant.students.cards', ['students' => collect([$student])]);
    }

    public function regeneratePortal(Student $student): RedirectResponse
    {
        $student->regenerateGuardianToken();

        return redirect()->route('tenant.students.show', $student)->with('status', __('messages.portal_link_regenerated'));
    }

    public function importForm(): View
    {
        return view('tenant.students.import');
    }

    /**
     * Bulk-import students from an uploaded CSV file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $client = $request->user()->client;
        $handle = fopen($request->file('file')->getRealPath(), 'r');

        $header = fgetcsv($handle) ?: [];
        $columns = $this->mapColumns($header);

        if (! isset($columns['name'])) {
            fclose($handle);

            return back()->withErrors(['file' => __('messages.import_missing_name_column')]);
        }

        $created = 0;
        $skipped = 0;
        $limitReached = false;

        while (($row = fgetcsv($handle)) !== false) {
            $name = trim((string) ($row[$columns['name']] ?? ''));

            if ($name === '') {
                $skipped++;

                continue;
            }

            if (! $client->canAddStudent()) {
                $limitReached = true;
                break;
            }

            Student::create([
                'name' => $name,
                'phone' => $this->cell($row, $columns, 'phone'),
                'guardian_phone' => $this->cell($row, $columns, 'guardian_phone'),
                'stage' => $this->cell($row, $columns, 'stage'),
            ]);

            $created++;
        }

        fclose($handle);

        $message = __('messages.students_imported', ['count' => $created]);
        if ($skipped > 0) {
            $message .= ' '.__('messages.rows_skipped', ['count' => $skipped]);
        }
        if ($limitReached) {
            $message .= ' '.__('messages.plan_student_limit');
        }

        return redirect()->route('tenant.students.index')->with('status', $message);
    }

    /**
     * Download a CSV template with the expected headers.
     */
    public function importTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['name', 'phone', 'guardian_phone', 'stage']);
            fputcsv($out, ['Ahmed Ali', '01000000000', '01100000000', 'Grade 10']);
            fclose($out);
        }, 'students-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Resolve column positions from the header row, tolerating English/Arabic labels and a UTF-8 BOM.
     *
     * @param  array<int, string>  $header
     * @return array<string, int>
     */
    private function mapColumns(array $header): array
    {
        $aliases = [
            'name' => ['name', 'الاسم'],
            'phone' => ['phone', 'الهاتف'],
            'guardian_phone' => ['guardian_phone', 'هاتف ولي الأمر'],
            'stage' => ['stage', 'المرحلة'],
        ];

        $map = [];

        foreach ($header as $index => $label) {
            $clean = mb_strtolower(trim(ltrim((string) $label, "\xEF\xBB\xBF")));

            foreach ($aliases as $key => $names) {
                if (in_array($clean, $names, true)) {
                    $map[$key] = $index;
                }
            }
        }

        return $map;
    }

    /**
     * @param  array<int, string>  $row
     * @param  array<string, int>  $columns
     */
    private function cell(array $row, array $columns, string $key): ?string
    {
        if (! isset($columns[$key])) {
            return null;
        }

        $value = trim((string) ($row[$columns[$key]] ?? ''));

        return $value === '' ? null : $value;
    }
}
