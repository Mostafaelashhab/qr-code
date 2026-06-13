<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\TestAnswerFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_id',
        'test_attempt_id',
        'question_id',
        'question_option_id',
        'is_correct',
    ];

    protected $attributes = [
        'is_correct' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<TestAttempt, $this>
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
