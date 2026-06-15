<?php

namespace App\Enums;

/**
 * Per-module abilities a center admin can grant to a custom staff role.
 *
 * Each case maps to a section of the tenant app. Center admins (client_admin)
 * implicitly hold every permission; custom staff roles (client_user) hold only
 * the subset granted to them. Admin-only areas (users, roles, settings, billing,
 * activity log) are intentionally not grantable.
 */
enum Permission: string
{
    case Students = 'students';
    case Groups = 'groups';
    case Subjects = 'subjects';
    case Teachers = 'teachers';
    case Attendance = 'attendance';
    case Exams = 'exams';
    case Timetable = 'timetable';
    case Payments = 'payments';
    case Expenses = 'expenses';
    case OnlineTests = 'online_tests';
    case Reports = 'reports';
    case Messages = 'messages';

    /**
     * Human-readable, translatable label for the permission.
     */
    public function label(): string
    {
        return __('permissions.'.$this->value);
    }

    /**
     * The optional plan feature this permission depends on, if any. A staff role
     * can only exercise the permission when the plan also grants the feature.
     */
    public function feature(): ?Feature
    {
        return match ($this) {
            self::Attendance => Feature::Attendance,
            self::Exams => Feature::Exams,
            self::Timetable => Feature::Timetable,
            self::Payments => Feature::Payments,
            self::Expenses => Feature::Expenses,
            self::OnlineTests => Feature::OnlineTests,
            self::Reports => Feature::Reports,
            self::Messages => Feature::Messages,
            default => null,
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
     * All permission keys, useful for a role that may do everything.
     *
     * @return array<int, string>
     */
    public static function allValues(): array
    {
        return array_map(fn (self $permission): string => $permission->value, self::cases());
    }
}
