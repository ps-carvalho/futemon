<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\IImportService;
use App\DTOs\PlayerImportDTO;
use App\Models\Country;
use App\Models\Player;
use App\Models\PlayerPosition;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class ImportDataService implements IImportService
{
    public Client $client;

    private string $apiKey;

    private string $baseUrl;

    private int $rate_limit;

    private int $timeout;

    private int $maxRetries;

    private int $baseDelayMs;

    private float $backoffMultiplier;

    public function __construct(?Client $client = null)
    {
        $this->apiKey = config('services.sportsmonks.key');
        $this->baseUrl = config('services.sportsmonks.url');
        $this->timeout = (int) config('services.sportsmonks.timeout', 30);
        $this->rate_limit = (int) config('services.sportsmonks.rate_limit', 100);
        $this->maxRetries = (int) config('services.sportsmonks.max_retries', 3);
        $this->baseDelayMs = (int) config('services.sportsmonks.base_delay_ms', 1000);
        $this->backoffMultiplier = (float) config('services.sportsmonks.backoff_multiplier', 2.0);

        $this->client = $client ?? new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ],
            'timeout' => $this->timeout,
            'rate_limit' => $this->rate_limit,
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function importPlayers(int $page): void
    {
        $endpoint = 'players';

        try {
            $response = $this->makeApiRequestWithRetry($endpoint, $page);
            $players = $this->processApiResponse($response);
            $this->storePlayers($players);
        } catch (Exception $exception) {
            Log::error('Failed to import players: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    private function makeApiRequestWithRetry(string $endpoint, int $page): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $this->maxRetries) {
            try {
                if ($attempt > 0) {
                    $delay = $this->calculateDelay($attempt);
                    Log::info(sprintf('Retrying API request (attempt %d/%d) after %dms delay', $attempt, $this->maxRetries, $delay), [
                        'endpoint' => $endpoint,
                        'page' => $page,
                    ]);
                    usleep($delay * 1000); // Convert ms to microseconds
                }

                return $this->makeApiRequest($endpoint, $page);
            } catch (GuzzleException $exception) {
                $lastException = $exception;
                ++$attempt;

                if ($this->shouldRetry($exception) && $attempt <= $this->maxRetries) {
                    Log::warning(sprintf('API request failed (attempt %d), will retry', $attempt), [
                        'endpoint' => $endpoint,
                        'page' => $page,
                        'error' => $exception->getMessage(),
                        'status_code' => $this->getStatusCode($exception),
                    ]);

                    continue;
                }

                Log::error(sprintf('API request failed after %d attempts', $attempt), [
                    'endpoint' => $endpoint,
                    'page' => $page,
                    'error' => $exception->getMessage(),
                    'status_code' => $this->getStatusCode($exception),
                ]);
                break;
            }
        }

        throw $lastException;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    private function makeApiRequest(string $endpoint, int $page): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => [
                    'page' => $page,
                    'per_page' => 25,
                    'include' => 'country;position',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $guzzleException) {
            Log::error(sprintf('API request failed for endpoint %s: ', $endpoint).$guzzleException->getMessage());
            throw $guzzleException;
        }
    }

    /**
     * Calculate delay for exponential backoff with optional jitter
     */
    private function calculateDelay(int $attempt): int
    {
        $exponentialDelay = $this->baseDelayMs * pow($this->backoffMultiplier, $attempt - 1);

        // Add jitter (random variation of ±25%) to prevent thundering herd
        $jitterRange = $exponentialDelay * 0.25;
        $jitter = rand((int) -$jitterRange, (int) $jitterRange);

        return max(0, (int) ($exponentialDelay + $jitter));
    }

    /**
     * Determine if the exception should trigger a retry
     */
    private function shouldRetry(GuzzleException $exception): bool
    {
        // Don't retry on client errors (4xx), but retry on server errors (5xx) and network issues
        if ($exception instanceof RequestException) {
            $response = $exception->getResponse();
            if ($response !== null) {
                $statusCode = $response->getStatusCode();

                // Don't retry on client errors (400-499), except for rate limiting (429)
                if ($statusCode >= 400 && $statusCode < 500 && $statusCode !== 429) {
                    return false;
                }

                // Retry on server errors (500-599) and rate limiting (429)
                return $statusCode >= 500 || $statusCode === 429;
            }
        }

        // Retry on network errors (connection timeouts, DNS failures, etc.)
        return true;
    }

    /**
     * Extract status code from exception for logging
     */
    private function getStatusCode(GuzzleException $exception): ?int
    {
        if ($exception instanceof RequestException) {
            $response = $exception->getResponse();

            return $response?->getStatusCode();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, PlayerImportDTO>
     *
     * @throws Exception
     */
    private function processApiResponse(array $response): array
    {
        if (! isset($response['data'])) {
            throw new RuntimeException('Invalid API response format');
        }

        try {
            return array_map(
                fn (array $playerData): PlayerImportDTO => PlayerImportDTO::fromApiData($playerData),
                $response['data']
            );
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param  array<int, PlayerImportDTO>  $players
     *
     * @throws Exception
     */
    private function storePlayers(array $players): void
    {
        try {
            DB::beginTransaction();

            foreach ($players as $playerData) {
                $country = Country::firstOrCreate(
                    [
                        'imported_id' => $playerData->country->imported_id,
                        'name' => $playerData->country->name,
                        'official_name' => $playerData->country->official_name,
                        'fifa_name' => $playerData->country->fifa_name,
                        'iso2' => $playerData->country->iso2,
                        'iso3' => $playerData->country->iso3,
                        'longitude' => $playerData->country->longitude,
                        'latitude' => $playerData->country->latitude,
                    ]
                );

                $position = null;
                if ($playerData->position !== null) {
                    $position = PlayerPosition::firstOrCreate(
                        [
                            'imported_id' => $playerData->position->imported_id,
                            'name' => $playerData->position->name,
                            'code' => $playerData->position->code,
                            'developer_name' => $playerData->position->developer_name,
                            'model_type' => $playerData->position->model_type,
                            'stat_group' => $playerData->position->stat_group,
                        ]
                    );
                }

                Player::updateOrCreate(
                    [
                        'imported_id' => $playerData->imported_id,
                    ],
                    [
                        'name' => $playerData->name,
                        'common_name' => $playerData->common_name,
                        'gender' => $playerData->gender,
                        'display_name' => $playerData->display_name,
                        'image_path' => $playerData->image_path,
                        'country_id' => $country->id,
                        'position_id' => $position?->id,
                        'date_of_birth' => $this->parseDateOfBirth($playerData->date_of_birth),
                        'height' => $playerData->height,
                        'weight' => $playerData->weight,
                    ]
                );
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Failed to store players: '.$exception->getMessage());
            throw $exception;
        }
    }

    private function parseDateOfBirth(?string $dateOfBirth): ?Carbon
    {
        if ($dateOfBirth === null || $dateOfBirth === '' || $dateOfBirth === '0') {
            return null;
        }

        return Carbon::parse($dateOfBirth);
    }
}
