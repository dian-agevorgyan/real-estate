<?php

declare(strict_types=1);

namespace App\Enums;

enum PremiseStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case NotForSale = 'not_for_sale';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Доступно',
            self::Reserved => 'Забронировано',
            self::Sold => 'Продано',
            self::NotForSale => 'Не в продаже',
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
