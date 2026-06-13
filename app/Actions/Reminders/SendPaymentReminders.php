<?php

namespace App\Actions\Reminders;

use App\Contracts\SmsGateway;
use App\Enums\SmsType;
use App\Models\Group;
use App\Models\Payment;
use App\Models\SmsMessage;

class SendPaymentReminders
{
    public function __construct(private SmsGateway $gateway) {}

    /**
     * Notify guardians of enrolled students who have not paid for the given month.
     * Returns the number of reminders sent.
     */
    public function execute(Group $group, string $month): int
    {
        $paidStudentIds = Payment::where('group_id', $group->id)
            ->where('for_month', $month)
            ->pluck('student_id')
            ->unique();

        $debtors = $group->students()
            ->wherePivot('is_active', true)
            ->whereNotNull('guardian_phone')
            ->whereNotIn('students.id', $paidStudentIds)
            ->get();

        foreach ($debtors as $student) {
            $body = __('messages_log.payment_due_body', [
                'name' => $student->name,
                'group' => $group->name,
                'amount' => number_format((float) $group->monthly_fee, 2),
                'month' => $month,
            ]);

            $ok = $this->gateway->send($student->guardian_phone, $body);

            SmsMessage::create([
                'client_id' => $group->client_id,
                'student_id' => $student->id,
                'to' => $student->guardian_phone,
                'type' => SmsType::PaymentDue,
                'body' => $body,
                'status' => $ok ? 'sent' : 'failed',
                'sent_at' => now(),
            ]);
        }

        return $debtors->count();
    }
}
