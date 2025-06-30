<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\ImportDataService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

final class ImportDataServiceTest extends TestCase
{
    private MockHandler $mockHandler;

    private Client $client;

    private ImportDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.sportsmonks.key', 'Laravel');
        Config::set('services.sportsmonks.url', '');
        Config::set('services.sportsmonks.timeout', 30);
        Config::set('services.sportsmonks.rate_limit', 100);

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $handlerStack]);
        $this->service = new ImportDataService($this->client);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_import_players(): void
    {
        $responseData = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Test Player',
                    'common_name' => 'Test Player',
                    'gender' => 'male',
                    'display_name' => 'Test Player',
                    'date_of_birth' => '1990-01-01',
                    'height' => 180,
                    'weight' => 75,
                    'image_path' => null,
                    'country' => [
                        'id' => 1,
                        'name' => 'Test Country',
                        'official_name' => 'Test Country',
                        'iso2' => 'EN',
                        'iso3' => 'ENG',
                        'latitude' => '51.507222',
                        'longitude' => '-0.1275',
                        'fifa_name' => 'Test Country',
                        'image_path' => 'https://cdn.sportmonks.com/images/flags/16x11/eng.png',
                    ],
                    'position' => [
                        'id' => 1,
                        'name' => 'Test Position',
                        'code' => 'GK',
                        'developer_name' => 'Test Position',
                        'model_type' => 'position',
                        'stat_group' => null,
                    ],
                ],
            ],
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $this->service->importPlayers(1);

        $this->assertDatabaseHas('players', [
            'id' => 1,
            'name' => 'Test Player',
            'common_name' => 'Test Player',
            'gender' => 'male',
            'display_name' => 'Test Player',
            'date_of_birth' => '1990-01-01',
            'height' => 180,
            'weight' => 75,
            'image_path' => null,
            'country_id' => 1,
            'position_id' => 1,
        ]);
    }
}
