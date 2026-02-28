<?php

declare(strict_types=1);

namespace App\Enums;

enum ComplexStatus: string
{
    case Planning = 'planning';
    case Construction = 'construction';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Планирование',
            self::Construction => 'Строительство',
            self::Completed => 'Сдан',
        };
    }

    public static function options(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}
