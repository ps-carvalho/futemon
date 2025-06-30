<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CountryImportDto;
use App\DTOs\PlayerImportDTO;
use App\DTOs\PositionImportDTO;
use App\Services\ImportDataService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Mockery;
use ReflectionClass;
use RuntimeException;
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
        Config::set('services.sportsmonks.max_retries', 3);
        Config::set('services.sportsmonks.base_delay_ms', 1000);
        Config::set('services.sportsmonks.backoff_multiplier', 2.0);

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
            'imported_id' => 1,
            'name' => 'Test Player',
            'common_name' => 'Test Player',
            'gender' => 'male',
            'display_name' => 'Test Player',
            'date_of_birth' => '1990-01-01 00:00:00',
            'height' => 180,
            'weight' => 75,
            'image_path' => null,
        ]);

        $this->assertDatabaseHas('countries', [
            'imported_id' => 1,
            'name' => 'Test Country',
            'official_name' => 'Test Country',
            'iso2' => 'EN',
            'iso3' => 'ENG',
        ]);

        $this->assertDatabaseHas('player_position', [
            'imported_id' => 1,
            'name' => 'Test Position',
            'code' => 'GK',
        ]);
    }

    public function test_import_players_with_exception(): void
    {
        // Add multiple exceptions to handle retry attempts
        $exception = new RequestException('Error communicating with server', new Request('GET', 'players'));

        // Add enough exceptions to cover all retry attempts (typically 3-4 attempts)
        $this->mockHandler->append(
            $exception,
            $exception,
            $exception,
            $exception  // Add one extra to be safe
        );

        $this->expectException(RequestException::class);

        $this->service->importPlayers(1);
    }

    public function test_make_api_request_with_retry_succeeds_after_retry(): void
    {
        // Use reflection to access private method
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('makeApiRequestWithRetry');
        $method->setAccessible(true);

        // First request fails with 500, second succeeds
        $this->mockHandler->append(
            new Response(500, [], '{"error": "Server error"}'),
            new Response(200, [], '{"data": []}')
        );

        $result = $method->invoke($this->service, 'players', 1);

        $this->assertEquals(['data' => []], $result);
        $this->assertEquals(0, $this->mockHandler->count());
    }

    public function test_make_api_request_with_retry_fails_after_max_retries(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('makeApiRequestWithRetry');
        $method->setAccessible(true);

        // All requests fail with 500
        $this->mockHandler->append(
            new Response(500, [], '{"error": "Server error"}'),
            new Response(500, [], '{"error": "Server error"}'),
            new Response(500, [], '{"error": "Server error"}'),
            new Response(500, [], '{"error": "Server error"}')
        );

        $this->expectException(\GuzzleHttp\Exception\ServerException::class);

        $method->invoke($this->service, 'players', 1);
    }

    public function test_make_api_request(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('makeApiRequest');
        $method->setAccessible(true);

        $responseData = ['data' => []];
        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $method->invoke($this->service, 'players', 1);

        $this->assertEquals($responseData, $result);
    }

    public function test_calculate_delay(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('calculateDelay');
        $method->setAccessible(true);

        // Test with different attempt numbers
        $delay1 = $method->invoke($this->service, 1);
        $delay2 = $method->invoke($this->service, 2);
        $delay3 = $method->invoke($this->service, 3);

        // Due to jitter, we can only check ranges
        $this->assertGreaterThanOrEqual(750, $delay1); // 1000 * 1 with possible -25% jitter
        $this->assertLessThanOrEqual(1250, $delay1);  // 1000 * 1 with possible +25% jitter

        $this->assertGreaterThanOrEqual(1500, $delay2); // 1000 * 2 with possible -25% jitter
        $this->assertLessThanOrEqual(2500, $delay2);  // 1000 * 2 with possible +25% jitter

        $this->assertGreaterThanOrEqual(3000, $delay3); // 1000 * 4 with possible -25% jitter
        $this->assertLessThanOrEqual(5000, $delay3);  // 1000 * 4 with possible +25% jitter
    }

    public function test_should_retry(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('shouldRetry');
        $method->setAccessible(true);

        // Test with different exception types
        $clientException = new RequestException(
            'Client error',
            new Request('GET', 'test'),
            new Response(400, [], '')
        );
        $serverException = new RequestException(
            'Server error',
            new Request('GET', 'test'),
            new Response(500, [], '')
        );
        $rateLimitException = new RequestException(
            'Rate limit exceeded',
            new Request('GET', 'test'),
            new Response(429, [], '')
        );
        $networkException = new ConnectException(
            'Network error',
            new Request('GET', 'test')
        );

        $this->assertFalse($method->invoke($this->service, $clientException));
        $this->assertTrue($method->invoke($this->service, $serverException));
        $this->assertTrue($method->invoke($this->service, $rateLimitException));
        $this->assertTrue($method->invoke($this->service, $networkException));
    }

    public function test_get_status_code(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('getStatusCode');
        $method->setAccessible(true);

        // Test with different exception types
        $requestException = new RequestException(
            'Request error',
            new Request('GET', 'test'),
            new Response(404, [], '')
        );
        $connectException = new ConnectException(
            'Connect error',
            new Request('GET', 'test')
        );

        $this->assertEquals(404, $method->invoke($this->service, $requestException));
        $this->assertNull($method->invoke($this->service, $connectException));
    }

    public function test_process_api_response_valid(): void
    {
        // Use reflection to access private method
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('processApiResponse');
        $method->setAccessible(true);

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

        $result = $method->invoke($this->service, $responseData);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(PlayerImportDTO::class, $result[0]);
        $this->assertEquals(1, $result[0]->imported_id);
        $this->assertEquals('Test Player', $result[0]->name);
        $this->assertInstanceOf(CountryImportDto::class, $result[0]->country);
        $this->assertInstanceOf(PositionImportDTO::class, $result[0]->position);
    }

    public function test_process_api_response_invalid(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('processApiResponse');
        $method->setAccessible(true);

        $responseData = ['invalid' => 'response'];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid API response format');

        $method->invoke($this->service, $responseData);
    }

    public function test_store_players(): void
    {
        // Use reflection to access private method
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('storePlayers');
        $method->setAccessible(true);

        $country = new CountryImportDto(
            imported_id: 1,
            name: 'Test Country',
            official_name: 'Test Country Official',
            fifa_name: 'Test Country FIFA',
            iso2: 'TC',
            iso3: 'TCO',
            longitude: 10.0,
            latitude: 20.0,
            image_path: 'path/to/image.png'
        );

        $position = new PositionImportDTO(
            imported_id: 1,
            name: 'Test Position',
            code: 'TP',
            developer_name: 'Test Position Dev',
            model_type: 'position',
            stat_group: null
        );

        $player = new PlayerImportDTO(
            imported_id: 1,
            name: 'Test Player',
            common_name: 'Test Player Common',
            gender: 'male',
            display_name: 'Test Player Display',
            image_path: 'path/to/player.png',
            country: $country,
            position: $position,
            date_of_birth: '1990-01-01',
            height: 180,
            weight: 75
        );

        $method->invoke($this->service, [$player]);

        $this->assertDatabaseHas('countries', [
            'imported_id' => 1,
            'name' => 'Test Country',
            'official_name' => 'Test Country Official',
        ]);

        $this->assertDatabaseHas('player_position', [
            'imported_id' => 1,
            'name' => 'Test Position',
            'code' => 'TP',
        ]);

        $this->assertDatabaseHas('players', [
            'imported_id' => 1,
            'name' => 'Test Player',
            'common_name' => 'Test Player Common',
        ]);
    }

    public function test_parse_date_of_birth_valid(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('parseDateOfBirth');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, '1990-01-01');

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals('1990-01-01', $result->toDateString());
    }

    public function test_parse_date_of_birth_invalid(): void
    {
        $reflectionMethod = new ReflectionClass(ImportDataService::class);
        $method = $reflectionMethod->getMethod('parseDateOfBirth');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($this->service, null));
        $this->assertNull($method->invoke($this->service, ''));
        $this->assertNull($method->invoke($this->service, '0'));
    }
}
