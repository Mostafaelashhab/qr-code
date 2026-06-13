<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ExamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    /** @use HasFactory<ExamFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    protected $fillable = [
        'group_id',
        'name',
        'exam_date',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'max_score' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function averageScore(): ?float
    {
        $avg = $this->grades()->avg('score');

        return $avg !== null ? round((float) $avg, 2) : null;
    }
}
