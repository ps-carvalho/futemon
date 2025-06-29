<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface IImportService
{
    public function importPlayers(int $page): void;
}
