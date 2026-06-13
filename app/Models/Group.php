<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'name',
        'monthly_fee',
        'teacher_share',
        'capacity',
        'schedule',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
        'monthly_fee' => 0,
        'teacher_share' => 0,
    ];

    protected function casts(): array
    {
        return [
            'monthly_fee' => 'decimal:2',
            'teacher_share' => 'decimal:2',
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<AttendanceSession, $this>
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * @return HasMany<Exam, $this>
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * @return HasMany<TimetableSlot, $this>
     */
    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function hasCapacity(): bool
    {
        return $this->capacity === null
            || $this->enrollments()->where('is_active', true)->count() < $this->capacity;
    }
}
