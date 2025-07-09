<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\FilterDefault;
use App\Enums\PaginationDefault;
use App\Enums\SortDefault;
use App\Enums\SortDirection;
use App\Models\Player;
use App\Repositories\PlayerRepository;
use App\Services\PlayersService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class PlayersList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $direction = SortDirection::ASC->value;

    public string $orderBy = SortDefault::ORDER_BY->value;

    public int $nationality = FilterDefault::COUNTRY_ID->value;

    public int $perPage = PaginationDefault::PER_PAGE->value;

    public int $page = 1;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'direction' => ['except' => SortDirection::ASC->value],
        'orderBy' => ['except' => SortDefault::ORDER_BY->value],
        'nationality' => ['except' => FilterDefault::COUNTRY_ID->value],
        'perPage' => ['except' => PaginationDefault::PER_PAGE->value],
        'page' => ['except' => 1],
    ];

    public function mount(
        int $page = 1,
        int $perPage = PaginationDefault::PER_PAGE->value,
        string $search = '',
        string $orderBy = SortDefault::ORDER_BY->value,
        string $direction = SortDirection::ASC->value,
        int $nationality = FilterDefault::COUNTRY_ID->value,
    ): void {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->orderBy = $orderBy;
        $this->direction = $direction;
        $this->nationality = $nationality;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedNationality(): void
    {
        $this->resetPage();
    }

    public function updatedOrderBy(): void
    {
        $this->resetPage();
    }

    public function updatedDirection(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'nationality', 'direction']);
        $this->resetPage();
    }

    public function goToPage(int $page): void
    {
        $this->setPage($page);
    }

    public function previousPage(): void
    {
        $this->setPage(max($this->getPage() - 1, 1));
    }

    public function nextPage(int $lastPage): void
    {
        $this->setPage(min($this->getPage() + 1, $lastPage));
    }

    /**
     * Get published players.
     *
     * @return LengthAwarePaginator<int, Player>
     */
    #[Computed]
    public function players(): LengthAwarePaginator
    {
        return app(PlayersService::class)->searchPlayers(
            $this->search,
            $this->nationality,
            $this->perPage,
            $this->orderBy,
            $this->direction
        );
    }

    /**
     * @return Collection<int, object>
     */
    #[Computed]
    public function nationalities(): Collection
    {
        return app(PlayerRepository::class)->getNationalities();
    }

    /**
     * @return Collection<int, object>
     */
    #[Computed]
    public function positions(): Collection
    {
        return app(PlayerRepository::class)->getPositions();
    }

    public function render(): View
    {
        return view('livewire.players-list');
    }
}
