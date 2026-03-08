<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum IntensityEnum: string implements TranslatableInterface
{

    case WARMUP = 'warmup';
    case DESIRE = 'desire';
    case SPARK = 'spark';
    case FIRE = 'fire';
    case ERUPTION = 'eruption';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey(), [], 'enums', $locale);
    }

    public function getTranslationKey(): string
    {
        return $this->value;
    }

    public static function casesWithLabels(TranslatorInterface $translator, ?string $locale = null): array
    {
        return array_map(
            fn(self $case) => [$case->value => $case->trans($translator, $locale)],
            self::cases()
        );
    }
}
