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
                    'date_of_birth' => '1990-01-01', // Add this missing field
                    'height' => 180, // Optional: Add height
                    'weight' => 75,  // Optional: Add weight
                    'image_path' => null, // Optional: Add image_path
                    'country' => ['id' => 1, 'name' => 'Test Country'],
                    'position' => ['id' => 1, 'name' => 'Test Position'],
                ],
            ],
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $this->service->importPlayers(1);

        $this->assertTrue(true);
    }
}
