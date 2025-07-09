<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\FilterDefault;
use App\Enums\PaginationDefault;
use App\Enums\SortDefault;
use App\Enums\SortDirection;
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

    public function index(): RedirectResponse
    {
        $appReady = Cache::get('app_setup_is_completed', false);
        if ($appReady) {
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
            'perPage' => $request->query->get('perPage') ?? PaginationDefault::PER_PAGE->value,
            'nationality' => $request->query->get('nationality') ?? FilterDefault::COUNTRY_ID->value,
            'orderBy' => $request->query->get('orderBy') ?? SortDefault::ORDER_BY->value,
            'direction' => $request->query->get('direction') ?? SortDirection::ASC->value,
            'search' => $request->query->get('search') ?? '',
        ]);
    }
}
