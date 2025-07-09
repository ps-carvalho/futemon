<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\FilterDefault;
use App\Enums\PaginationDefault;
use App\Enums\SortDefault;
use App\Enums\SortDirection;
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
        int $countryId = FilterDefault::COUNTRY_ID->value,
        int $perPage = PaginationDefault::PER_PAGE->value,
        string $orderBy = SortDefault::ORDER_BY->value,
        string $direction = SortDirection::ASC->value): LengthAwarePaginator;

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
