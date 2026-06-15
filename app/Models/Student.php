<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'guardian_phone',
        'reminders_opt_out',
        'stage',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
        'reminders_opt_out' => false,
    ];

    protected static function booted(): void
    {
        static::creating(function (Student $student): void {
            $student->qr_token ??= (string) Str::uuid();
            $student->guardian_token ??= (string) Str::uuid();
        });
    }

    /**
     * Public, shareable parent-portal URL for this student.
     * Self-heals tokens for students created before the column existed.
     */
    public function portalUrl(): string
    {
        if (blank($this->guardian_token)) {
            $this->regenerateGuardianToken();
        }

        return route('portal.show', $this->guardian_token);
    }

    public function regenerateGuardianToken(): void
    {
        $this->forceFill(['guardian_token' => (string) Str::uuid()])->save();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'reminders_opt_out' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<Group, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'enrollments')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Charge, $this>
     */
    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Total owed after discounts.
     */
    public function totalCharged(): float
    {
        return (float) $this->charges()->sum('amount') - (float) $this->charges()->sum('discount');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Outstanding balance: positive means the student still owes, negative is credit.
     */
    public function balance(): float
    {
        return $this->totalCharged() - $this->totalPaid();
    }
}
