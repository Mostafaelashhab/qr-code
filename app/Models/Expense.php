<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use BelongsToClient, HasFactory, LogsActivity;

    public function activityTitle(): ?string
    {
        return $this->title;
    }

    protected $fillable = [
        'title',
        'category',
        'amount',
        'spent_at',
        'note',
    ];

    protected $attributes = [
        'category' => ExpenseCategory::Other->value,
    ];

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'decimal:2',
            'spent_at' => 'date',
        ];
    }

    public function scopeSpentBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('spent_at', [$from, $to]);
    }
}
