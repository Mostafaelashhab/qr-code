<?php

namespace Database\Seeders;

use App\Actions\Clients\CreateClientWithOwner;
use App\Actions\Subscriptions\StartSubscription;
use App\Enums\AttendanceStatus;
use App\Enums\BillingPeriod;
use App\Enums\Feature;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Charge;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(CreateClientWithOwner $createClient, StartSubscription $startSubscription): void
    {
        // Platform super admin.
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
        ]);

        // Subscription plans.
        $plans = collect([
            ['name' => 'Starter', 'price' => 199, 'billing_period' => BillingPeriod::Monthly, 'max_users' => 5, 'max_students' => 100, 'sort_order' => 1,
                'features' => [Feature::Attendance->value, Feature::Payments->value]],
            ['name' => 'Growth', 'price' => 499, 'billing_period' => BillingPeriod::Monthly, 'max_users' => 20, 'max_students' => 500, 'sort_order' => 2,
                'features' => [Feature::Attendance->value, Feature::Payments->value, Feature::Expenses->value, Feature::Exams->value, Feature::Reports->value]],
            ['name' => 'Pro', 'price' => 4990, 'billing_period' => BillingPeriod::Yearly, 'max_users' => null, 'max_students' => null, 'sort_order' => 3,
                'features' => Feature::allValues()],
        ])->map(fn (array $attributes): Plan => Plan::create([
            ...$attributes,
            'slug' => str($attributes['name'])->slug(),
            'description' => $attributes['name'].' plan',
        ]));

        // Demo tenant with an admin, a staff user and an active subscription.
        $client = $createClient->execute(
            clientData: [
                'name' => 'Al-Noor Tutoring Center',
                'email' => 'info@alnoor.test',
                'phone' => '+201000000000',
                'address' => 'Cairo, Egypt',
                'is_active' => true,
            ],
            ownerData: [
                'name' => 'Center Admin',
                'email' => 'center@example.com',
                'phone' => '+201000000001',
                'password' => 'password',
            ],
        );

        $client->users()->create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'role' => UserRole::ClientUser,
            'password' => 'password',
        ]);

        // Pro plan unlocks every feature so the demo center can try the whole system.
        $startSubscription->execute($client, $plans->firstWhere('name', 'Pro'));

        $this->seedDemoDomain($client);
    }

    /**
     * Seed a small tutoring dataset for the demo center: subjects, teachers,
     * students, groups with enrolled students, one attendance session and payments.
     */
    private function seedDemoDomain(Client $client): void
    {
        $math = Subject::create(['client_id' => $client->id, 'name' => 'Mathematics', 'stage' => 'Grade 12']);
        Subject::create(['client_id' => $client->id, 'name' => 'Physics', 'stage' => 'Grade 12']);

        $teacher = Teacher::create([
            'client_id' => $client->id,
            'subject_id' => $math->id,
            'name' => 'Mr. Ahmed',
            'phone' => '+201111111111',
        ]);

        $group = Group::create([
            'client_id' => $client->id,
            'subject_id' => $math->id,
            'teacher_id' => $teacher->id,
            'name' => 'Math - Group A',
            'monthly_fee' => 300,
            'teacher_share' => 50,
            'capacity' => 20,
            'schedule' => 'Sun/Tue 4:00 PM',
        ]);

        $students = Student::factory()->count(6)->create(['client_id' => $client->id]);

        foreach ($students as $index => $student) {
            Enrollment::create([
                'client_id' => $client->id,
                'group_id' => $group->id,
                'student_id' => $student->id,
                'enrolled_at' => now()->subWeeks(2),
            ]);

            // Monthly fee charged to the student.
            Charge::create([
                'client_id' => $client->id,
                'student_id' => $student->id,
                'group_id' => $group->id,
                'title' => 'Monthly fee',
                'amount' => $group->monthly_fee,
                'for_month' => now()->format('Y-m'),
            ]);

            // Most students paid in full; a couple still owe (to showcase balances).
            if ($index % 3 !== 0) {
                Payment::create([
                    'client_id' => $client->id,
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                    'amount' => $group->monthly_fee,
                    'for_month' => now()->format('Y-m'),
                    'paid_at' => now()->subDays(3),
                ]);
            }
        }

        $session = AttendanceSession::create([
            'client_id' => $client->id,
            'group_id' => $group->id,
            'session_date' => now()->subDays(2),
        ]);

        foreach ($students as $index => $student) {
            Attendance::create([
                'client_id' => $client->id,
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => $index % 4 === 0 ? AttendanceStatus::Absent : AttendanceStatus::Present,
            ]);
        }

        $this->seedDemoTest($client, $group);
    }

    /**
     * Seed one published online test with two questions for the demo group.
     */
    private function seedDemoTest(Client $client, Group $group): void
    {
        $test = Test::create([
            'client_id' => $client->id,
            'group_id' => $group->id,
            'token' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Quiz 1 — Algebra',
            'duration_minutes' => 20,
            'is_published' => true,
        ]);

        $q1 = Question::create([
            'client_id' => $client->id, 'test_id' => $test->id, 'body' => 'What is 2 + 2?', 'points' => 1, 'sort_order' => 1,
        ]);
        foreach (['3' => false, '4' => true, '5' => false, '22' => false] as $body => $correct) {
            $q1->options()->create(['client_id' => $client->id, 'body' => $body, 'is_correct' => $correct]);
        }

        $q2 = Question::create([
            'client_id' => $client->id, 'test_id' => $test->id, 'body' => 'A right angle is 90 degrees.',
            'type' => \App\Enums\QuestionType::TrueFalse, 'points' => 1, 'sort_order' => 2,
        ]);
        $q2->options()->create(['client_id' => $client->id, 'body' => 'True', 'is_correct' => true]);
        $q2->options()->create(['client_id' => $client->id, 'body' => 'False', 'is_correct' => false]);
    }
}
