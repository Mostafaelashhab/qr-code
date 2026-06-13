<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Database\Factories\TestAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    /** @use HasFactory<TestAttemptFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_id',
        'test_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Test, $this>
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return HasMany<TestAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function percentage(): ?int
    {
        if ($this->max_score === null || (float) $this->max_score === 0.0) {
            return null;
        }

        return (int) round((float) $this->score / (float) $this->max_score * 100);
    }
}
