<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function students(): StreamedResponse
    {
        $headers = [__('ui.name'), __('ui.stage'), __('ui.phone'), __('ui.guardian_phone'), __('ui.status')];

        return $this->csv('students.csv', $headers, function ($out): void {
            Student::orderBy('name')->chunk(200, function ($students) use ($out): void {
                foreach ($students as $student) {
                    fputcsv($out, [
                        $student->name,
                        $student->stage,
                        $student->phone,
                        $student->guardian_phone,
                        $student->is_active ? __('ui.active') : __('ui.inactive'),
                    ]);
                }
            });
        });
    }

    public function payments(): StreamedResponse
    {
        $headers = [__('ui.student'), __('ui.group'), __('ui.amount'), __('ui.method'), __('ui.for_month'), __('ui.paid_at')];

        return $this->csv('payments.csv', $headers, function ($out): void {
            Payment::with(['student', 'group'])->latest('paid_at')->chunk(200, function ($payments) use ($out): void {
                foreach ($payments as $payment) {
                    fputcsv($out, [
                        $payment->student->name,
                        $payment->group?->name,
                        $payment->amount,
                        $payment->method->label(),
                        $payment->for_month,
                        $payment->paid_at->toDateString(),
                    ]);
                }
            });
        });
    }

    /**
     * Stream a UTF-8 CSV download (with BOM for spreadsheet compatibility).
     *
     * @param  array<int, string>  $headers
     * @param  callable(resource): void  $writeRows
     */
    private function csv(string $filename, array $headers, callable $writeRows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $writeRows): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($out, $headers);
            $writeRows($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
