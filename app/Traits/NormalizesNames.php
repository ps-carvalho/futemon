<?php

declare(strict_types=1);

namespace App\Traits;

trait NormalizesNames
{
    /**
     * Normalize a name for sorting by removing accents and converting to lowercase
     */
    public static function normalizeName(string $name): string
    {
        // Remove accents and diacritics
        $normalized = transliterator_transliterate('Any-Latin; Latin-ASCII', $name);
        return mb_strtolower($normalized, 'UTF-8');
    }
}
