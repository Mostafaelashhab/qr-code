<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Database\Factories\QuestionOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    /** @use HasFactory<QuestionOptionFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_id',
        'question_id',
        'body',
        'is_correct',
        'sort_order',
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
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
