<?php

namespace App\Actions\Reminders;

use App\Actions\Reminders\Concerns\QueuesThrottledReminders;
use App\Contracts\MessageGateway;
use App\Enums\SmsType;
use App\Models\Group;
use App\Models\Payment;
use App\Models\SmsMessage;

class SendPaymentReminders
{
    use QueuesThrottledReminders;

    public function __construct(private MessageGateway $gateway) {}

    /**
     * Queue reminders to guardians of enrolled students who have not paid for
     * the given month. Skips guardians who have opted out. Returns the number
     * queued.
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
            ->where('students.reminders_opt_out', false)
            ->whereNotIn('students.id', $paidStudentIds)
            ->get();

        $group->loadMissing('client');
        $cursor = 0;

        foreach ($debtors as $student) {
            $body = __('messages_log.payment_due_body', [
                'name' => $student->name,
                'group' => $group->name,
                'amount' => number_format((float) $group->monthly_fee, 2),
                'month' => $month,
            ]);

            $message = SmsMessage::create([
                'client_id' => $group->client_id,
                'student_id' => $student->id,
                'to' => $student->guardian_phone,
                'type' => SmsType::PaymentDue,
                'channel' => $this->gateway->channel(),
                'body' => $body,
                'status' => 'queued',
            ]);

            $this->dispatchReminder($message, $cursor);
        }

        return $debtors->count();
    }
}
