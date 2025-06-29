<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\IImportService;
use App\Models\Country;
use App\Models\Player;
use App\Models\PlayerPosition;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    public function __construct(Client $client = null)
    {
        $this->apiKey = config('services.sportsmonks.key');
        $this->baseUrl = config('services.sportsmonks.url');
        $this->timeout = (int) config('services.sportsmonks.timeout', 30);
        $this->rate_limit = (int) config('services.sportsmonks.rate_limit', 100);

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
            $response = $this->makeApiRequest($endpoint, $page);
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
    private function makeApiRequest(string $endpoint, int $page): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => [
                    'page' => $page,
                    'per_page' => 1000,
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
     * @param  array<string, mixed>  $response
     * @return array<int, mixed>
     */
    private function processApiResponse(array $response): array
    {
        if (! isset($response['data'])) {
            throw new RuntimeException('Invalid API response format');
        }

        return $response['data'];
    }

    /**
     * @param  array<int, mixed>  $players
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
                        'imported_id' => $playerData['country']['id'],
                        'name' => $playerData['country']['name'] ?? null,
                        'official_name' => $playerData['country']['official_name'] ?? null,
                        'fifa_name' => $playerData['country']['fifa_name'] ?? null,
                        'iso2' => $playerData['country']['iso2'] ?? null,
                        'iso3' => $playerData['country']['iso3'] ?? null,
                        'longitude' => $playerData['country']['longitude'] ?? null,
                        'latitude' => $playerData['country']['latitude'] ?? null,
                    ]
                );
                if (isset($playerData['position']['id'])) {
                    $position = PlayerPosition::firstOrCreate(
                        [
                            'imported_id' => $playerData['position']['id'],
                            'name' => $playerData['position']['name'] ?? null,
                            'code' => $playerData['position']['code'] ?? null,
                            'developer_name' => $playerData['position']['developer_name'] ?? null,
                            'model_type' => $playerData['position']['model_type'] ?? null,
                            'stat_group' => $playerData['position']['stat_group'] ?? null,
                        ]
                    );
                }

                Player::updateOrCreate(
                    [
                        'imported_id' => $playerData['id'],
                        'name' => $playerData['name'],
                        'common_name' => $playerData['common_name'],
                        'gender' => $playerData['gender'],
                        'display_name' => $playerData['display_name'],
                        'image_path' => $playerData['image_path'] ?? null,
                        'country_id' => $country->id,
                        'position_id' => $position->id ?? null,
                        'date_of_birth' => $playerData['date_of_birth'] ?? null,
                        'height' => $playerData['height'] ?? null,
                        'weight' => $playerData['weight'] ?? null,
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
}
