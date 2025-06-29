<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlayersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_home_route_with_default_parameters(): void
    {
        $response = $this->get('/');

        $response->assertViewHas([
            'page' => 1,
            'perPage' => 12,
            'nationality' => '',
            'orderBy' => 'name',
            'direction' => 'asc',
            'search' => '',
        ]);
    }

    public function test_home_route_with_custom_parameters(): void
    {
        $response = $this->get('/?page=2&perPage=24&nationality=US&orderBy=created_at&direction=desc&search=john');

        $response->assertViewHas([
            'page' => 2,
            'perPage' => 24,
            'nationality' => 'US',
            'orderBy' => 'created_at',
            'direction' => 'desc',
            'search' => 'john',
        ]);
    }

    public function test_home_route_with_search_parameter(): void
    {
        $response = $this->get('/?search=test');

        $response->assertViewHas('search', 'test');
    }

    public function test_home_route_with_pagination_parameters(): void
    {
        $response = $this->get('/?page=3&perPage=48');

        $response->assertViewHas([
            'page' => 3,
            'perPage' => 48,
        ]);
    }

    public function test_home_route_with_sorting_parameters(): void
    {
        $response = $this->get('/?orderBy=age&direction=desc');

        $response->assertViewHas([
            'orderBy' => 'age',
            'direction' => 'desc',
        ]);
    }
}
