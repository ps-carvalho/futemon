<div class="container mx-auto max-w-2xl px-4 pt-[25%]">
    <div class="player-card bg-white rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl cursor-pointer">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-8 relative">
            @if($isProcessing)
                <div class="absolute inset-0  flex items-center justify-center">
                    <div class="text-white text-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto mb-2"></div>
                        <p class="text-sm">{{ $processingMessage }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Setup Info -->
        <div class="p-6 pt-4">
            @if(!$isProcessing)
            <h3 class="text-xl font-bold text-gray-800 text-center mb-4">Let's setup your application</h3>
            @endif

            <!-- Card Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-center space-x-16">
                    <button
                        wire:click="seedWithMockedData"
                        wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        @if($isProcessing) disabled @endif
                    >
                        <span wire:loading.remove wire:target="seedWithMockedData">Seed with Mocked data</span>
                        <span wire:loading wire:target="seedWithMockedData">Processing...</span>
                    </button>
                    <button
                        wire:click="seedWithSportsMonksData"
                        wire:loading.attr="disabled"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        @if($isProcessing) disabled @endif
                    >
                        <span wire:loading.remove wire:target="seedWithSportsMonksData">Seed with SportsMonks Data</span>
                        <span wire:loading wire:target="seedWithSportsMonksData">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('start-job-polling', () => {
                const interval = setInterval(() => {
                @this.checkJobStatus();
                }, 2000); // Check every 2 seconds

                // Stop polling after 5 minutes to prevent infinite polling
                setTimeout(() => {
                    clearInterval(interval);
                }, 300000);
            });
        });
    </script>
</div>
