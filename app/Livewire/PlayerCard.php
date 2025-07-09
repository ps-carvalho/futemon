<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Player;
use Illuminate\View\View;
use Livewire\Component;

final class PlayerCard extends Component
{
    /**
     *  The player instance
     *
     * @var Player
     */
    public Player $player;

    /**
     *  Modal open state
     *
     * @var bool
     */
    public bool $showModal = false;

    /**
     *  Open Modal
     *
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     *  Close Modal
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     *  Player card view
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.player-card');
    }
}
