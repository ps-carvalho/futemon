<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\IPlayerRepository;
use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PlayerRepository implements IPlayerRepository
{
    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    public function searchPlayers(?string $search, int $countryId = 0, int $perPage = 12, string $orderBy = 'name', string $direction = 'asc'): LengthAwarePaginator
    {
        return Player::query()
            ->with('country', 'position')
            ->when($countryId, fn ($q) => $q->whereHas('country', fn ($subQuery) => $subQuery->where('id', $countryId)))
            ->when($search, fn ($q) => $q->search($search))
            ->orderBy($orderBy, $direction)
            ->paginate($perPage);
    }

    public function getById(int $id): ?Player
    {
        return Player::query()
            ->with('country', 'position')
            ->find($id);
    }

    /**
     * @return Collection<int, object>
     */
    public function getNationalities(): Collection
    {
        return Cache::remember('player_nationalities', 86400, function () {
            return Player::query()
                ->select('countries.name', 'countries.id')
                ->join('countries', 'players.country_id', '=', 'countries.id')
                ->whereNotNull('countries.name')
                ->distinct()
                ->orderBy('countries.name')
                ->get();
        });
    }

    /**
     * @return Collection<int, object>
     */
    public function getPositions(): Collection
    {
        return Cache::remember('player_positions', 86400, function () {
            return Player::query()
                ->join('player_positions', 'players.position_id', '=', 'player_positions.id')
                ->select('player_positions.name', 'player_positions.id')
                ->distinct()
                ->orderBy('player_positions.name')
                ->get();
        });
    }

    public function create(): void {}
}
