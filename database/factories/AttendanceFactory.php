<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Client;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'attendance_session_id' => fn (array $attributes) => AttendanceSession::factory()->create(['client_id' => $attributes['client_id']])->id,
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'status' => fake()->randomElement(AttendanceStatus::cases()),
        ];
    }
}
