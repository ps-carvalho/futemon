<?php

namespace App\Contracts\Services;

interface IImportService
{
    public function importPlayers(int $page): void;
}
