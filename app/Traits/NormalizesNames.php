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

        // Fallback if intl extension is not available
        if ($normalized === false) {
            $normalized = self::removeAccentsFallback($name);
        }

        return mb_strtolower($normalized, 'UTF-8');
    }

    /**
     * Fallback method to remove accents without intl extension
     */
    private static function removeAccentsFallback(string $string): string
    {
        $unwanted = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ñ' => 'n', 'ç' => 'c', 'ß' => 'ss', 'ð' => 'd', 'þ' => 'th', 'ł' => 'l',
            // Add uppercase variants
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y', 'Ÿ' => 'Y',
            'Ñ' => 'N', 'Ç' => 'C', 'Ð' => 'D', 'Þ' => 'TH', 'Ł' => 'L',
        ];

        return strtr($string, $unwanted);
    }
}
