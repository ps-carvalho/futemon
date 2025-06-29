<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Player;
use Illuminate\View\View;
use Livewire\Component;

final class PlayerCard extends Component
{
    public Player $player;

    public bool $showModal = false;

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.player-card');
    }
}
