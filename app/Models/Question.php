<?php

namespace App\Models;

use App\Enums\QuestionType;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_id',
        'test_id',
        'body',
        'type',
        'points',
        'sort_order',
    ];

    protected $attributes = [
        'type' => QuestionType::Mcq->value,
        'points' => 1,
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'points' => 'integer',
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
     * @return HasMany<QuestionOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order')->orderBy('id');
    }
}
