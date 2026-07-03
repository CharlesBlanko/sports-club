@extends('layouts.app')

@php
    $periodLabels = [
        'month' => 'Ce mois',
        'last_month' => 'Le mois passé',
        'year' => 'Cette année',
        'all' => 'Depuis toujours',
    ];

    $sportStyles = [
        'Run' => ['🏃', 'bg-clay text-white'],
        'Ride' => ['🚴', 'bg-lake text-white'],
        'VirtualRide' => ['🚴', 'bg-lake text-white'],
        'Walk' => ['🚶', 'bg-moss text-white'],
        'Hike' => ['🥾', 'bg-moss text-white'],
        'Swim' => ['🏊', 'bg-sky-600 text-white'],
        'Workout' => ['◆', 'bg-asphalt text-white'],
    ];

    $sortUrl = function (string $column) use ($sort, $direction, $period) {
        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $sort === $column && $direction === 'desc' ? 'asc' : 'desc',
            'period' => $period,
        ]);
    };

    $formatDuration = function (int $seconds) {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        return $hours.' h '.str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    };
@endphp

@section('content')
    <section class="mb-8 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
        <div>
            <p class="text-sm font-semibold uppercase text-clay">Tableau de bord</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-asphalt sm:text-4xl">Activités du club</h1>
        </div>
        @auth
            <div class="rounded border border-black/10 bg-white px-4 py-3 text-sm text-black/65">
                Connecté: <span class="font-semibold text-asphalt">{{ auth()->user()->name }}</span>
                @if (auth()->user()->last_synced_at)
                    <span class="block text-xs">Dernière synchro: {{ auth()->user()->last_synced_at->diffForHumans() }}</span>
                @endif
            </div>
        @else
            <div class="rounded border border-clay/25 bg-white px-4 py-3 text-sm text-black/65">
                Connectez-vous avec Strava pour synchroniser vos activités.
            </div>
        @endauth
    </section>

    <section class="mb-10" id="calendrier">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-asphalt">Calendrier</h2>
            <div class="flex items-center gap-2">
                <a class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold hover:border-lake hover:text-lake" href="{{ request()->fullUrlWithQuery(['calendar' => $previousMonth]) }}">←</a>
                <span class="min-w-40 text-center text-sm font-bold capitalize text-asphalt">{{ $calendarMonth->translatedFormat('F Y') }}</span>
                <a class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold hover:border-lake hover:text-lake" href="{{ request()->fullUrlWithQuery(['calendar' => $nextMonth]) }}">→</a>
            </div>
        </div>

        <div class="overflow-hidden rounded border border-black/10 bg-white">
            <div class="grid grid-cols-7 border-b border-black/10 bg-asphalt text-xs font-bold uppercase text-white">
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
                                    @php($style = $sportStyles[$sport['sport']] ?? ['◆', 'bg-asphalt text-white'])
                                    <button class="activity-dot relative grid size-8 place-items-center rounded text-sm {{ $style[1] }}" aria-label="{{ $sport['sport'] }}">
                                        <span>{{ $style[0] }}</span>
                                        <span class="activity-tooltip absolute left-1/2 top-9 z-10 w-48 -translate-x-1/2 rounded bg-asphalt px-3 py-2 text-left text-xs font-medium text-white shadow-lg">
                                            <span class="block font-bold">{{ $sport['sport'] }} · {{ $sport['count'] }}</span>
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

    <section class="mb-10" id="groupe">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-asphalt">Groupe</h2>
            <form method="get" class="flex items-center gap-2">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <select name="period" class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold" onchange="this.form.submit()">
                    @foreach ($periodLabels as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto rounded border border-black/10 bg-white">
            <table class="min-w-full divide-y divide-black/10 text-sm">
                <thead class="bg-black/[0.03] text-left text-xs uppercase text-black/60">
                    <tr>
                        <th class="px-4 py-3"><a href="{{ $sortUrl('name') }}">Membre</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('moving_time') }}">Temps</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('activities_count') }}">Activités</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('distance') }}">Distance</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('elevation') }}">Élévation</a></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse ($members as $member)
                        <tr class="{{ $member['id'] === $currentUserId ? 'bg-lake/10' : '' }}">
                            <td class="px-4 py-3 font-semibold text-asphalt">
                                <div class="flex items-center gap-3">
                                    @if ($member['profile'])
                                        <img src="{{ $member['profile'] }}" alt="" class="size-9 rounded object-cover">
                                    @else
                                        <span class="grid size-9 place-items-center rounded bg-black/10 text-xs">{{ Str::of($member['name'])->substr(0, 2)->upper() }}</span>
                                    @endif
                                    {{ $member['name'] }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $formatDuration($member['moving_time']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['activities_count'], 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['distance'] / 1000, 1, ',', ' ') }} km</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['elevation'], 0, ',', ' ') }} m</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-black/55">Aucune activité synchronisée pour cette période.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="activites">
        <h2 class="mb-4 text-xl font-bold text-asphalt">Activités</h2>
        <div class="grid gap-3">
            @forelse ($latestActivities as $activity)
                <article class="rounded border border-black/10 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-asphalt">{{ $activity->name }}</h3>
                            <p class="text-sm text-black/60">{{ $activity->user->name }} · {{ $activity->started_at->translatedFormat('j F Y, H:i') }}</p>
                        </div>
                        <span class="rounded bg-black/[0.06] px-3 py-1 text-xs font-bold uppercase text-black/65">{{ $activity->sport_type ?? $activity->type ?? 'Autre' }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                        <div><span class="block text-xs uppercase text-black/50">Distance</span><strong>{{ number_format($activity->distance / 1000, 1, ',', ' ') }} km</strong></div>
                        <div><span class="block text-xs uppercase text-black/50">Temps</span><strong>{{ $formatDuration($activity->moving_time) }}</strong></div>
                        <div><span class="block text-xs uppercase text-black/50">Élévation</span><strong>{{ number_format($activity->total_elevation_gain, 0, ',', ' ') }} m</strong></div>
                    </div>
                </article>
            @empty
                <div class="rounded border border-black/10 bg-white p-8 text-center text-black/55">Les dernières activités apparaîtront ici après la première synchronisation.</div>
            @endforelse
        </div>
    </section>
@endsection
