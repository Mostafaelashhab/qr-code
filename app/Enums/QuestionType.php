<?php

namespace App\Enums;

enum QuestionType: string
{
    case Mcq = 'mcq';
    case TrueFalse = 'true_false';

    public function label(): string
    {
        return match ($this) {
            self::Mcq => __('tests.type.mcq'),
            self::TrueFalse => __('tests.type.true_false'),
        };
    }
}
