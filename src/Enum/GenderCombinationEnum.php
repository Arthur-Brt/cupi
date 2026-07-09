<?php

namespace App\Enum;

enum GenderCombinationEnum: string
{
    case ANY = 'any';
    case HOMME_FEMME = 'homme_femme';
    case HOMME_HOMME = 'homme_homme';
    case FEMME_FEMME = 'femme_femme';

    public function label(): string
    {
        return match ($this) {
            self::ANY => 'Peu importe',
            self::HOMME_FEMME => 'Un homme + une femme',
            self::HOMME_HOMME => 'Deux hommes',
            self::FEMME_FEMME => 'Deux femmes',
        };
    }
}
