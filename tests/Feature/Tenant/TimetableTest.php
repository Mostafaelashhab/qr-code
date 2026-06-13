<?php

use App\Models\Group;
use App\Models\Teacher;
use App\Models\TimetableSlot;

it('adds a timetable slot to a group', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post(route('tenant.timetable.store', $group), [
        'weekday' => 1,
        'start_time' => '16:00',
        'end_time' => '17:30',
        'room' => 'Room 1',
    ])->assertRedirect();

    expect(TimetableSlot::withoutGlobalScopes()->where('group_id', $group->id)->count())->toBe(1);
});

it('rejects an overlapping slot for the same teacher', function () {
    [$client, $admin] = tenantWithAdmin();
    $teacher = Teacher::factory()->create(['client_id' => $client->id]);
    $groupA = Group::factory()->create(['client_id' => $client->id, 'teacher_id' => $teacher->id]);
    $groupB = Group::factory()->create(['client_id' => $client->id, 'teacher_id' => $teacher->id]);

    TimetableSlot::factory()->create([
        'client_id' => $client->id,
        'group_id' => $groupA->id,
        'weekday' => 1,
        'start_time' => '16:00',
        'end_time' => '17:30',
    ]);

    $this->actingAs($admin)->post(route('tenant.timetable.store', $groupB), [
        'weekday' => 1,
        'start_time' => '17:00',
        'end_time' => '18:00',
    ])->assertSessionHasErrors('start_time');

    expect(TimetableSlot::withoutGlobalScopes()->count())->toBe(1);
});

it('allows the same time for a different teacher', function () {
    [$client, $admin] = tenantWithAdmin();
    $groupA = Group::factory()->create(['client_id' => $client->id, 'teacher_id' => Teacher::factory()->create(['client_id' => $client->id])->id]);
    $groupB = Group::factory()->create(['client_id' => $client->id, 'teacher_id' => Teacher::factory()->create(['client_id' => $client->id])->id]);

    TimetableSlot::factory()->create(['client_id' => $client->id, 'group_id' => $groupA->id, 'weekday' => 1, 'start_time' => '16:00', 'end_time' => '17:30']);

    $this->actingAs($admin)->post(route('tenant.timetable.store', $groupB), [
        'weekday' => 1,
        'start_time' => '16:00',
        'end_time' => '17:30',
    ])->assertSessionHasNoErrors();

    expect(TimetableSlot::withoutGlobalScopes()->count())->toBe(2);
});

it('renders the weekly timetable', function () {
    [$client, $admin] = tenantWithAdmin();
    $group = Group::factory()->create(['client_id' => $client->id, 'name' => 'Slot Group']);
    TimetableSlot::factory()->create(['client_id' => $client->id, 'group_id' => $group->id, 'weekday' => 2]);

    $this->actingAs($admin)->get(route('tenant.timetable.index'))
        ->assertOk()
        ->assertSee('Slot Group');
});
