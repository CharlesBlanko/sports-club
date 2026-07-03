@extends('layouts.app')

@php
    $sportStyles = [
        'Run' => 'bg-clay text-white',
        'TrailRun' => 'bg-clay text-white',
        'VirtualRun' => 'bg-clay text-white',
        'Ride' => 'bg-lake text-white',
        'VirtualRide' => 'bg-lake text-white',
        'MountainBikeRide' => 'bg-lake text-white',
        'GravelRide' => 'bg-lake text-white',
        'EBikeRide' => 'bg-lake text-white',
        'EMountainBikeRide' => 'bg-lake text-white',
        'Walk' => 'bg-moss text-white',
        'Hike' => 'bg-moss text-white',
        'Swim' => 'bg-sky-600 text-white',
        'Workout' => 'bg-asphalt text-white',
        'WeightTraining' => 'bg-asphalt text-white',
        'Yoga' => 'bg-asphalt text-white',
    ];
@endphp

@section('content')
    @include('dashboard.partials.heading', [
        'eyebrow' => 'Tableau de bord',
        'title' => 'Calendrier du club',
    ])

    <section>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-asphalt">Calendrier</h2>
            <div class="flex items-center gap-2">
                <a class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold hover:border-lake hover:text-lake" href="{{ request()->fullUrlWithQuery(['calendar' => $previousMonth]) }}" aria-label="Mois precedent">&larr;</a>
                <span class="min-w-40 text-center text-sm font-bold capitalize text-asphalt">{{ $calendarMonth->translatedFormat('F Y') }}</span>
                <a class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold hover:border-lake hover:text-lake" href="{{ request()->fullUrlWithQuery(['calendar' => $nextMonth]) }}" aria-label="Mois suivant">&rarr;</a>
            </div>
        </div>

        <div class="overflow-visible rounded border border-black/10 bg-white">
            <div class="grid grid-cols-7 rounded-t border-b border-black/10 bg-asphalt text-xs font-bold uppercase text-white">
                @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                    <div class="px-3 py-2">{{ $dayName }}</div>
                @endforeach
            </div>
            @foreach ($calendar as $week)
                <div class="grid grid-cols-7 border-b border-black/10 last:border-b-0">
                    @foreach ($week as $day)
                        <div class="min-h-28 border-r border-black/10 p-2 last:border-r-0 {{ $day['in_month'] ? 'bg-white' : 'bg-black/[0.03]' }}">
                            <div class="mb-2 text-xs font-bold {{ $day['date']->isToday() ? 'text-clay' : 'text-black/55' }}">{{ $day['date']->day }}</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($day['sports'] as $sport)
                                    @php($style = $sportStyles[$sport['sport']] ?? 'bg-asphalt text-white')
                                    <button class="activity-dot relative grid size-8 place-items-center rounded {{ $style }}" style="padding: 3px;" aria-label="{{ $sport['sport'] }}">
                                        @include('dashboard.partials.sport-icon', ['sport' => $sport['sport']])
                                        <span class="activity-tooltip absolute left-1/2 top-9 z-10 w-48 -translate-x-1/2 rounded bg-asphalt px-3 py-2 text-left text-xs font-medium text-white shadow-lg">
                                            <span class="block font-bold">{{ $sport['sport'] }} &middot; {{ $sport['count'] }}</span>
                                            {{ $sport['members']->join(', ') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
@endsection
