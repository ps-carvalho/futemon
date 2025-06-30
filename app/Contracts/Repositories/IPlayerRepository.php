<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Constants\CountryConstants;
use App\Constants\PlayerConstants;
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
    public function searchPlayers(
        string $search,
        int $countryId = CountryConstants::DEFAULT_ID,
        int $perPage = PlayerConstants::DEFAULT_PER_PAGE,
        string $orderBy = PlayerConstants::DEFAULT_ORDER_BY,
        string $direction = PlayerConstants::DEFAULT_DIRECTION): LengthAwarePaginator;

    public function getById(int $id): ?Player;

    /**
     * @return Collection<int, object>
     */
    public function getNationalities(): Collection;

    /**
     * @return Collection<int, object>
     */
    public function getPositions(): Collection;
}
