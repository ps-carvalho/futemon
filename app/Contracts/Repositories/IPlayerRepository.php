<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IPlayerRepository
{
    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    public function searchPlayers(string $search, int $countryId = 0, int $perPage = 12, string $orderBy = 'name', string $direction = 'asc'): LengthAwarePaginator;

    public function getById(int $id): ?Player;

    /**
     * @return Collection<int, object>
     */
    public function getNationalities(): Collection;

    /**
     * @return Collection<int, object>
     */
    public function getPositions(): Collection;

    public function create(): void;
}
