<?php

namespace App\Actions\Reminders;

use App\Actions\Reminders\Concerns\QueuesThrottledReminders;
use App\Contracts\MessageGateway;
use App\Enums\AttendanceStatus;
use App\Enums\SmsType;
use App\Models\AttendanceSession;
use App\Models\SmsMessage;

class SendAbsenceReminders
{
    use QueuesThrottledReminders;

    public function __construct(private MessageGateway $gateway) {}

    /**
     * Queue reminders to the guardians of students marked absent in a session.
     * Skips guardians who have opted out. Returns the number queued.
     */
    public function execute(AttendanceSession $session): int
    {
        $session->loadMissing(['client', 'group', 'attendances.student']);

        $absences = $session->attendances
            ->where('status', AttendanceStatus::Absent)
            ->filter(fn ($attendance) => filled($attendance->student?->guardian_phone)
                && ! $attendance->student->reminders_opt_out);

        $cursor = 0;

        foreach ($absences as $attendance) {
            $body = __('messages_log.absence_body', [
                'name' => $attendance->student->name,
                'group' => $session->group->name,
                'date' => $session->session_date->isoFormat('LL'),
            ]);

            $message = SmsMessage::create([
                'client_id' => $session->client_id,
                'student_id' => $attendance->student_id,
                'to' => $attendance->student->guardian_phone,
                'type' => SmsType::Absence,
                'channel' => $this->gateway->channel(),
                'body' => $body,
                'status' => 'queued',
            ]);

            $this->dispatchReminder($message, $cursor);
        }

        return $absences->count();
    }
}
