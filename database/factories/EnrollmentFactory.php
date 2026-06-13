<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'group_id' => fn (array $attributes) => Group::factory()->create(['client_id' => $attributes['client_id']])->id,
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'enrolled_at' => now(),
            'is_active' => true,
        ];
    }
}
