<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\IPlayerService;
use App\Services\SportsMonksService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PlayersController extends Controller
{
    public function setup(): View
    {
        return view('welcome');
    }

    public function index(IPlayerService $playerService): \Illuminate\Http\RedirectResponse
    {
        $players = $playerService->searchPlayers(
            search: '',
        );
        if ($players->count() > 0) {
            return redirect()->route('players');
        }

        return redirect()->route('setup');

    }

    public function players(Request $request): View
    {
        return view('players', [
            'page' => $request->query->get('page') ?? 1,
            'perPage' => $request->query->get('perPage') ?? 12,
            'nationality' => $request->query->get('nationality') ?? 0,
            'orderBy' => $request->query->get('orderBy') ?? 'name',
            'direction' => $request->query->get('direction') ?? 'asc',
            'search' => $request->query->get('search') ?? '',
        ]);
    }
}
