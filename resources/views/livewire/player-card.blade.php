<div>
    <!-- Existing Player Card (with click handler) -->
    <div
        wire:click="openModal"
        class="player-card bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300 hover:shadow-3xl cursor-pointer"
    >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-4 relative">
            <div class="absolute top-2 right-2  text-white text-xs font-bold px-2 py-1 rounded-full">
                {{ $player->position->name }}
            </div>
        </div>

        <!-- Player Photo -->
        <div class="relative -mt-8 flex justify-center">
            <div class="w-24 h-24 bg-white rounded-full p-2 shadow-lg">
                <img src="{{ $player->image_path }}" alt="{{ $player->name }}" class="w-full h-full rounded-full object-cover">
            </div>
        </div>

        <!-- Player Info -->
        <div class="p-6 pt-4">
            <h3 class="text-xl font-bold text-gray-800 text-center mb-4">{{ $player->name }}</h3>

            <!-- Card Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-center">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                        Profile
                    </div>
                </div>
            </div>

            <!-- Click to View More -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500 italic">Click to view details</p>
            </div>
        </div>
    </div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="$wire.showModal">
            <div class="fixed inset-0 bg-gray-200 opacity-85 blur-2xl transition-opacity"></div>
            <div class="flex items-center justify-center min-h-screen">
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
                    <!-- Player Card in Modal -->
                    <div class="p-6">
                        <!-- Card Header -->
                        <div
                            class="bg-gradient-to-r from-yellow-400 to-orange-500 p-4 -mx-6 -mt-6 rounded-t-lg relative">
                            <div class="absolute top-2 right-2 text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $player->position->name }}
                            </div>
                        </div>

                        <!-- Player Photo -->
                        <div class="relative -mt-8 flex justify-center">
                            <div class="w-24 h-24 bg-white rounded-full p-2 shadow-lg">
                                <img src="{{ $player->image_path }}" alt="{{ $player->name }}"
                                     class="w-full h-full rounded-full object-cover">
                            </div>
                        </div>

                        <!-- Player Info -->
                        <div class="pt-4">
                            <h3 class="text-xl font-bold text-gray-800 text-center mb-4">{{ $player->name }}</h3>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Full name :</span>
                                    <span class="text-gray-800 font-bold">{{ $player->common_name }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Gender :</span>
                                    <span class="text-gray-800 font-bold">{{ $player->gender }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Height :</span>
                                    <span class="text-gray-800 font-bold">{{ $player->height }} cm</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Weight :</span>
                                    <span class="text-gray-800 font-bold">{{ $player->weight }} kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Age:</span>
                                    <span class="text-gray-800 font-bold">{{ $player->getAge() }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Nationality:</span>
                                    <span class="text-gray-800 font-bold">{{ $player->country->name }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Position:</span>
                                    <span class="text-gray-800 font-bold">{{ $player->position->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                        <button type="button" wire:click="closeModal"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>
