<?php

declare(strict_types=1);

namespace App\Services;

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
    public function searchPlayers(?string $search, int $countryCode = 0, int $perPage = 12, string $orderBy = 'name', string $direction = 'asc'): LengthAwarePaginator
    {
        return $this->playerRepository->searchPlayers($search, $countryCode, $perPage, $orderBy, $direction);
    }

    public function getById(int $id): ?Player
    {
        return $this->playerRepository->getById($id);
    }
}
