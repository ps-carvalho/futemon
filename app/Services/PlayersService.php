<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\IPlayerRepository;
use App\Contracts\Services\IPlayerService;
use App\Enums\FilterDefault;
use App\Enums\PaginationDefault;
use App\Enums\SortDefault;
use App\Enums\SortDirection;
use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;

final class PlayersService implements IPlayerService
{
    private IPlayerRepository $playerRepository;

    public function __construct(IPlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    public function searchPlayers(
        ?string $search,
        int $countryId = FilterDefault::COUNTRY_ID->value,
        int $perPage = PaginationDefault::PER_PAGE->value,
        string $orderBy = SortDefault::ORDER_BY->value,
        string $direction = SortDirection::ASC->value): LengthAwarePaginator
    {
        return $this->playerRepository->searchPlayers($search, $countryId, $perPage, $orderBy, $direction);
    }

    public function getById(int $id): ?Player
    {
        return $this->playerRepository->getById($id);
    }
}
