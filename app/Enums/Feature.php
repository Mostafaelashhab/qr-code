<?php

namespace App\Enums;

/**
 * Optional modules a subscription plan can grant. Core management (students,
 * groups, subjects, teachers, users, settings, billing) is always available;
 * these are the gateable, plan-controlled features.
 */
enum Feature: string
{
    case Attendance = 'attendance';
    case Exams = 'exams';
    case Timetable = 'timetable';
    case Payments = 'payments';
    case Expenses = 'expenses';
    case Reports = 'reports';
    case Messages = 'messages';
    case Activity = 'activity';
    case OnlineTests = 'online_tests';

    public function label(): string
    {
        return match ($this) {
            self::Attendance => __('ui.attendance'),
            self::Exams => __('ui.exams'),
            self::Timetable => __('ui.timetable'),
            self::Payments => __('ui.payments'),
            self::Expenses => __('ui.expenses'),
            self::Reports => __('ui.reports'),
            self::Messages => __('ui.messages'),
            self::Activity => __('ui.activity_log'),
            self::OnlineTests => __('ui.online_tests'),
        };
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        return self::cases();
    }

    /**
     * All feature keys, useful for plans that include everything.
     *
     * @return array<int, string>
     */
    public static function allValues(): array
    {
        return array_map(fn (self $feature): string => $feature->value, self::cases());
    }
}
