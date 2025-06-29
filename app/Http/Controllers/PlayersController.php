<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\IPlayerService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PlayersController extends Controller
{
    public function index(Request $request): View
    {
        return view('welcome', [
            'page' => $request->query->get('page') ?? 1,
            'perPage' => $request->query->get('perPage') ?? 12,
            'nationality' => $request->query->get('nationality') ?? '',
            'orderBy' => $request->query->get('orderBy') ?? 'name',
            'direction' => $request->query->get('direction') ?? 'asc',
            'search' => $request->query->get('search') ?? '',
        ]);
    }

    public function show(IPlayerService $playerService, int $id): View
    {
        $player = $playerService->getById($id);

        return view('show', ['player' => $player]);
    }
}
