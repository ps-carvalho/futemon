<?php

declare(strict_types=1);

namespace App\Enums;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function default(): self
    {
        return self::ASC;
    }
}
