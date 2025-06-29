@extends('layouts.app')

@section('title', $title ?? 'Players')

@section('content')
    <livewire:players-list
        :page="(int) $page"
        :perPage="(int) $perPage"
        :search="$search"
        :orderBy="$orderBy"
        :direction="$direction"
        :countryCode="$nationality"
    />
@endsection
