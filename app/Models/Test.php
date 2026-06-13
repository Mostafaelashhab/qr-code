<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Database\Factories\TestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Test extends Model
{
    /** @use HasFactory<TestFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_id',
        'group_id',
        'token',
        'title',
        'duration_minutes',
        'available_from',
        'available_to',
        'shuffle',
        'show_results',
        'is_published',
    ];

    protected $attributes = [
        'duration_minutes' => 30,
        'shuffle' => true,
        'show_results' => true,
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'available_from' => 'datetime',
            'available_to' => 'datetime',
            'shuffle' => 'boolean',
            'show_results' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Test $test): void {
            $test->token ??= (string) Str::uuid();
        });
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<TestAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }

    /**
     * Whether the test is currently open for taking.
     */
    public function isAvailable(): bool
    {
        if (! $this->is_published) {
            return false;
        }

        $now = now();

        return (! $this->available_from || $now->gte($this->available_from))
            && (! $this->available_to || $now->lte($this->available_to));
    }
}
