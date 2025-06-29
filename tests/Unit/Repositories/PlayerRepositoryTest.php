<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Contracts\Repositories\IPlayerRepository;
use App\Models\Player;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

final class PlayerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private IPlayerRepository $playerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->playerRepository = Mockery::mock(IPlayerRepository::class);
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

        $result = $this->playerRepository->searchPlayers($search, $countryCode, $perPage, $orderBy, $direction);

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

        $result = $this->playerRepository->getById($id);

        $this->assertInstanceOf(Player::class, $result);
        $this->assertEquals($id, $result->id);
    }
}
