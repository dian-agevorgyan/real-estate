<?php

declare(strict_types=1);

namespace App\Enums;

enum PremiseType: string
{
    case Apartment = 'apartment';
    case Studio = 'studio';
    case Penthouse = 'penthouse';
    case Commercial = 'commercial';

    public function label(): string
    {
        return match ($this) {
            self::Apartment => 'Квартира',
            self::Studio => 'Студия',
            self::Penthouse => 'Пентхаус',
            self::Commercial => 'Коммерческое',
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
