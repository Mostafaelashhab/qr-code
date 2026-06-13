<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Rent = 'rent';
    case Salaries = 'salaries';
    case Utilities = 'utilities';
    case Marketing = 'marketing';
    case Supplies = 'supplies';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Rent => __('expenses.category.rent'),
            self::Salaries => __('expenses.category.salaries'),
            self::Utilities => __('expenses.category.utilities'),
            self::Marketing => __('expenses.category.marketing'),
            self::Supplies => __('expenses.category.supplies'),
            self::Other => __('expenses.category.other'),
        };
    }
}
