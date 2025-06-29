<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Contracts\Repositories\IPlayerRepository;
use App\Models\Player;
use App\Services\PlayersService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

final class PlayersServiceTest extends TestCase
{
    private IPlayerRepository $playerRepository;

    private PlayersService $playersService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->playerRepository = Mockery::mock(IPlayerRepository::class);
        $this->playersService = new PlayersService($this->playerRepository);
    }

    public function test_search_players(): void
    {
        $search = 'John';
        $countryCode = 'US';
        $perPage = 12;
        $orderBy = 'name';
        $direction = 'asc';

        $paginator = new LengthAwarePaginator(
            [new Player()],
            1,
            $perPage,
            1
        );

        $this->playerRepository->shouldReceive('searchPlayers')
            ->with($search, $countryCode, $perPage, $orderBy, $direction)
            ->once()
            ->andReturn($paginator);

        $result = $this->playersService->searchPlayers($search, $countryCode, $perPage, $orderBy, $direction);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result->items());
    }

    public function test_get_by_id(): void
    {
        $id = 1;
        $player = new Player();
        $player->id = $id;
        $this->playerRepository->shouldReceive('getById')
            ->with($id)
            ->once()
            ->andReturn($player);
        $result = $this->playersService->getById($id);
        $this->assertInstanceOf(Player::class, $result);
        $this->assertEquals($id, $result->id);
    }
}
