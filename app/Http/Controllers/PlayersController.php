<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\IPlayerService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class PlayersController extends Controller
{
    /**
     * @return Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|object
     */
    public function setup()
    {
        $appReady = Cache::get('app_setup_is_completed', false);
        if ($appReady) {
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

    /**
     * @return Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|object
     */
    public function players(Request $request)
    {
        $appReady = Cache::get('app_setup_is_completed', false);
        if ($appReady === false) {
            return redirect()->route('setup');
        }

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
