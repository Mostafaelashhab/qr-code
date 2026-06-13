<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Excused = 'excused';

    public function label(): string
    {
        return match ($this) {
            self::Present => __('attendance.status.present'),
            self::Absent => __('attendance.status.absent'),
            self::Late => __('attendance.status.late'),
            self::Excused => __('attendance.status.excused'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present => 'emerald',
            self::Absent => 'rose',
            self::Late => 'amber',
            self::Excused => 'indigo',
        };
    }
}
