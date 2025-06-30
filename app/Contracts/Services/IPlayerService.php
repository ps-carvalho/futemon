<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPlayerService
{
    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    public function searchPlayers(?string $search, int $countryCode = 0, int $perPage = 12, string $orderBy = 'name', string $direction = 'asc'): LengthAwarePaginator;

    public function getById(int $id): ?Player;
}
