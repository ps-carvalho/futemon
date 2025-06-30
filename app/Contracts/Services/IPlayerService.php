<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Constants\CountryConstants;
use App\Constants\PlayerConstants;
use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPlayerService
{
    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    public function searchPlayers(
        ?string $search,
        int $countryId = CountryConstants::DEFAULT_ID,
        int $perPage = PlayerConstants::DEFAULT_PER_PAGE,
        string $orderBy = PlayerConstants::DEFAULT_ORDER_BY,
        string $direction = PlayerConstants::DEFAULT_DIRECTION): LengthAwarePaginator;

    public function getById(int $id): ?Player;
}
