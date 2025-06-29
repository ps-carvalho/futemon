@extends('layouts.app')

@section('title', $title ?? 'Players')

@section('content')
<div class="container mx-auto max-w-2xl px-4 pt-[25%]">
    <div
        class="player-card bg-white rounded-2xl shadow-2xl overflow-hidden  hover:shadow-3xl cursor-pointer"
    >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-4 relative">

        </div>

        <!-- Player Info -->
        <div class="p-6 pt-4">
            <h3 class="text-xl font-bold text-gray-800 text-center mb-4">let's setup your application</h3>

            <!-- Card Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-center space-x-16">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                        Seed with Mocked data
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                        Seed with SportsMonks Data
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
