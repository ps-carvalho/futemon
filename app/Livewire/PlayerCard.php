<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Player;
use Illuminate\View\View;
use Livewire\Component;

final class PlayerCard extends Component
{
    /**
     * @var Player
     */
    public Player $player;

    /**
     * @var bool
     */
    public bool $showModal = false;

    /**
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('livewire.player-card');
    }
}
