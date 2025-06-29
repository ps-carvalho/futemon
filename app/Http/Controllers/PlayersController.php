<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\IPlayerService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

final class PlayersController extends Controller
{
    /**
     * @return Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|object
     */
    public function setup()
    {
        $dataPopulated = (bool) Cache::get('data_populated');
        if ($dataPopulated === true) {
            return redirect()->route('players');
        }

        return view('welcome');
    }

    public function index(IPlayerService $playerService): RedirectResponse
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
