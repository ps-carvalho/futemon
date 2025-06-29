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

final class SportsMonksService implements IImportService
{
    private Client $client;

    private string $apiKey;

    private string $baseUrl;

    private int $rate_limit;

    private int $timeout;


    private function getClient()
    {
        $this->apiKey = 'LlxQTR2Nse9NGUFDcyBryuNtBJQ31H6q3kGUIQMFMn094VrGEUVTRGyTjIGh';
        $this->baseUrl = 'https://api.sportmonks.com/v3/football/';
        $this->timeout = 30;
        $this->rate_limit = 100;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function importPlayers(int $page): void
    {
        try {
            $this->getClient();
            $response = $this->makeApiRequest('players', $page);
            $players = $this->processApiResponse($response);
            $this->storePlayers($players);
        } catch (Exception $e) {
            Log::error('Failed to import players: '.$e->getMessage());
            throw $e;
        }
    }

    private function makeApiRequest(string $endpoint, int $page): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => [
                'page' => $page,
                'per_page' => 1000,
                'include' => 'country;position'
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error("API request failed for endpoint {$endpoint}: ".$e->getMessage());
            throw $e;
        }
    }

    private function processApiResponse(array $response): array
    {
        if (! isset($response['data'])) {
            throw new RuntimeException('Invalid API response format');
        }

        return $response['data'];
    }

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

                $position = PlayerPosition::firstOrCreate(
                    [
                        'imported_id' => $playerData['country']['id'],
                        'name' => $playerData['position']['name'] ?? null,
                        'code' => $playerData['position']['code'] ?? null,
                        'developer_name' => $playerData['position']['developer_name'] ?? null,
                        'model_type' => $playerData['position']['model_type'] ?? null,
                        'stat_group' => $playerData['position']['stat_group'] ?? null,
                    ]
                );

                Player::updateOrCreate(
                    [
                        'imported_id' => $playerData['id'],
                        'name' => $playerData['name'],
                        'common_name' => $playerData['common_name'],
                        'gender' => $playerData['gender'],
                        'display_name' => $playerData['display_name'],
                        'image_path' => $playerData['image_path'] ?? null,
                        'country_id' => $country->id,
                        'position_id' => $position->id,
                        'date_of_birth' => $playerData['date_of_birth'] ?? null,
                        'height' => $playerData['height'] ?? null,
                        'weight' => $playerData['weight'] ?? null,
                    ]
                );
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to store players: ' . $e->getMessage());
            throw $e;
        }
    }
}
