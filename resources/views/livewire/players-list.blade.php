
<div class="h-screen overflow-hidden">

    <div class="container mx-auto px-4 my-8">
        <!-- Search and Filter Controls -->
        <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 mb-8 shadow-xl">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <label for="search" class="block text-white font-medium mb-2">Search Players</label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name..."
                        class="w-full px-4 py-3 rounded-xl border-2 border-white/30 bg-white/90 text-gray-800 placeholder-gray-500 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50 transition-all"
                    >
                </div>

                <div class="md:w-64">
                    <label class="block text-white font-medium mb-2">Order By</label>
                    <select
                        wire:model.live="orderBy"
                        class="w-full px-4 py-3 rounded-xl border-2 border-white/30 bg-white/90 text-gray-800 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50 transition-all"
                    >
                        <option value="name">Name</option>
                        <option value="date_of_birth">Age</option>
                    </select>
                </div>
                <div>
                    <label class="block text-white font-medium mb-2">Direction</label>
                    <select
                        wire:model.live="direction"
                        class="w-full px-4 py-3 rounded-xl border-2 border-white/30 bg-white/90 text-gray-800 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50 transition-all"
                    >
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>

                <!-- Nationality Filter -->
                <div class="md:w-64">
                    <label class="block text-white font-medium mb-2">Filter by Nationality</label>
                    <select
                        wire:model.live="nationality"
                        class="w-full px-4 py-3 rounded-xl border-2 border-white/30 bg-white/90 text-gray-800 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50 transition-all"
                    >
                        <option value="0">All Nationalities</option>
                        @foreach($this->nationalities as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Clear Filters Button -->
            @if($search || $nationality)
                <div class="mt-4">
                    <button
                        wire:click="clearFilters"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-colors duration-150"
                    >
                        Clear Filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Player Cards Grid -->
        <div class="pr-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-h-[calc(100vh-420px)] overflow-y-scroll overflow-x-hidden">
            @foreach($this->players as $player)
                <livewire:player-card :player="$player" :key="$player->id" />
            @endforeach
        </div>

        <!-- Pagination Component -->
        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 bg-white/20 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
            <!-- Results Counter -->
            <div class="text-white font-medium">
                Showing <span class="font-bold text-yellow-300">{{ $this->players->firstItem() ?: 0 }}</span> to
                <span class="font-bold text-yellow-300">{{ $this->players->lastItem() ?: 0 }}</span> of
                <span class="font-bold text-yellow-300">{{ $this->players->total() }}</span> players
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center space-x-1">
                <!-- Previous Page Button -->
                <button wire:click="previousPage"
                        @if($this->players->onFirstPage()) disabled @endif
                        class="px-3 py-2 rounded-lg border-2 border-white/30 bg-white/10 text-white font-medium hover:bg-white/20 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Page Numbers -->
                <div class="hidden md:flex space-x-1">
                    @php
                        $window = 2; // How many numbers to show on each side of current page
                        $lastPage = $this->players->lastPage();
                        $currentPage = $this->players->currentPage();
                        $start = max(1, $currentPage - $window);
                        $end = min($lastPage, $currentPage + $window);
                    @endphp

                        <!-- First Page -->
                    @if($start > 1)
                        <button wire:click="goToPage(1)"
                                class="px-4 py-2 rounded-lg border-2 border-white/30 bg-white/10 text-white font-medium hover:bg-white/20 transition-colors duration-150">
                            1
                        </button>

                        @if($start > 2)
                            <span class="px-4 py-2 text-white font-medium">...</span>
                        @endif
                    @endif

                    <!-- Page Window -->
                    @for($i = $start; $i <= $end; $i++)
                        <button wire:click="goToPage({{ $i }})"
                                class="px-4 py-2 rounded-lg border-2 {{ $i === $currentPage ? 'border-yellow-400 bg-yellow-400 text-gray-900' : 'border-white/30 bg-white/10 text-white hover:bg-white/20' }} font-medium transition-colors duration-150">
                            {{ $i }}
                        </button>
                    @endfor

                    <!-- Last Page -->
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="px-4 py-2 text-white font-medium">...</span>
                        @endif

                        <button wire:click="goToPage({{ $lastPage }})"
                                class="px-4 py-2 rounded-lg border-2 border-white/30 bg-white/10 text-white font-medium hover:bg-white/20 transition-colors duration-150">
                            {{ $lastPage }}
                        </button>
                    @endif
                </div>

                <!-- Current Page Indicator (Mobile) -->
                <div class="flex md:hidden items-center">
                    <span class="px-4 py-2 rounded-lg border-2 border-yellow-400 bg-yellow-400 text-gray-900 font-medium">
                        {{ $this->players->currentPage() }}
                    </span>
                    <span class="px-2 text-white">of</span>
                    <button wire:click="goToPage({{ $this->players->lastPage() }})"
                            class="px-4 py-2 rounded-lg border-2 border-white/30 bg-white/10 text-white font-medium hover:bg-white/20 transition-colors duration-150">
                        {{ $this->players->lastPage() }}
                    </button>
                </div>

                <!-- Next Page Button -->
                <button wire:click="nextPage({{ $this->players->lastPage() }})"
                        @if(!$this->players->hasMorePages()) disabled @endif
                        class="px-3 py-2 rounded-lg border-2 border-white/30 bg-white/10 text-white font-medium hover:bg-white/20 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Per Page Selector -->
            <div class="flex items-center space-x-2">
                <label for="perPage" class="text-white font-medium">Show:</label>
                <select id="perPage" wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border-2 border-white/30 bg-white/90 text-gray-800 font-medium focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50 transition-all">
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="36">36</option>
                    <option value="48">48</option>
                </select>
            </div>
        </div>

    </div>

</div>
