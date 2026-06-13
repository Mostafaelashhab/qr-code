<?php

namespace App\Actions\Reminders;

use App\Contracts\SmsGateway;
use App\Enums\AttendanceStatus;
use App\Enums\SmsType;
use App\Models\AttendanceSession;
use App\Models\SmsMessage;

class SendAbsenceReminders
{
    public function __construct(private SmsGateway $gateway) {}

    /**
     * Notify the guardians of students marked absent in a session.
     * Returns the number of reminders sent.
     */
    public function execute(AttendanceSession $session): int
    {
        $session->loadMissing(['group', 'attendances.student']);

        $absences = $session->attendances
            ->where('status', AttendanceStatus::Absent)
            ->filter(fn ($attendance) => filled($attendance->student?->guardian_phone));

        foreach ($absences as $attendance) {
            $body = __('messages_log.absence_body', [
                'name' => $attendance->student->name,
                'group' => $session->group->name,
                'date' => $session->session_date->isoFormat('LL'),
            ]);

            $ok = $this->gateway->send($attendance->student->guardian_phone, $body);

            SmsMessage::create([
                'client_id' => $session->client_id,
                'student_id' => $attendance->student_id,
                'to' => $attendance->student->guardian_phone,
                'type' => SmsType::Absence,
                'body' => $body,
                'status' => $ok ? 'sent' : 'failed',
                'sent_at' => now(),
            ]);
        }

        return $absences->count();
    }
}
