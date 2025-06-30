<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\CountryConstants;
use App\Constants\PlayerConstants;
use App\Contracts\Repositories\IPlayerRepository;
use App\Contracts\Services\IPlayerService;
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
        int $countryId = CountryConstants::DEFAULT_ID,
        int $perPage = PlayerConstants::DEFAULT_PER_PAGE,
        string $orderBy = PlayerConstants::DEFAULT_ORDER_BY,
        string $direction = PlayerConstants::DEFAULT_DIRECTION): LengthAwarePaginator
    {
        return $this->playerRepository->searchPlayers($search, $countryId, $perPage, $orderBy, $direction);
    }

    public function getById(int $id): ?Player
    {
        return $this->playerRepository->getById($id);
    }
}
