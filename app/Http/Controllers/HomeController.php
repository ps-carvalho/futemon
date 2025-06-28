<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\IPlayerService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function index(IPlayerService $playerService): View
    {
        return view('welcome');
    }
}
