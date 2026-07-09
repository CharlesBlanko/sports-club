@extends('layouts.app')

@php
    $periodLabels = [
        'week' => 'Cette semaine',
        'last_week' => 'La semaine pass&eacute;e',
        'month' => 'Ce mois',
        'last_month' => 'Le mois pass&eacute;',
        'year' => 'Cette ann&eacute;e',
        'last_year' => "L'ann&eacute;e pass&eacute;e",
        'all' => 'Depuis toujours',
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

    $initials = function (string $name) {
        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => \Illuminate\Support\Str::of($part)->substr(0, 1)->upper())
            ->join('');
    };
@endphp

@section('content')
    @include('dashboard.partials.heading', [
        'eyebrow' => 'Classement',
        'title' => 'Groupe',
    ])

    <section>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-asphalt">Groupe</h2>
            <form method="get" class="flex items-center gap-2">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <select name="period" class="rounded border border-black/15 bg-white px-3 py-2 text-sm font-semibold" onchange="this.form.submit()">
                    @foreach ($periodLabels as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{!! $label !!}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mb-4 rounded border border-black/10 bg-white px-4 py-3">
            <span class="block text-xs font-bold uppercase text-black/55">Total</span>
            <div class="group-total-grid mt-2 text-sm">
                <div>
                    <span class="block text-xs uppercase text-black/50">Activit&eacute;s</span>
                    <strong class="text-asphalt">{{ number_format($totals['activities_count'], 0, ',', ' ') }}</strong>
                </div>
                <div>
                    <span class="block text-xs uppercase text-black/50">Temps</span>
                    <strong class="text-asphalt">{{ $formatDuration($totals['moving_time']) }}</strong>
                </div>
                <div>
                    <span class="block text-xs uppercase text-black/50">Blocs d'&eacute;nergie</span>
                    <strong class="energy-block-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="energy-block-icon" aria-hidden="true">
                            <path d="M13 2l-9 12h7l-1 8l10 -13h-7l0 -7z" />
                        </svg>
                        <span>{{ number_format($totals['energy_blocks'], 1, ',', ' ') }}</span>
                    </strong>
                </div>
                <div>
                    <span class="block text-xs uppercase text-black/50">Distance</span>
                    <strong class="text-asphalt">{{ number_format($totals['distance'] / 1000, 1, ',', ' ') }} km</strong>
                </div>
                <div>
                    <span class="block text-xs uppercase text-black/50">&Eacute;l&eacute;vation</span>
                    <strong class="text-asphalt">{{ number_format($totals['elevation'], 0, ',', ' ') }} m</strong>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded border border-black/10 bg-white">
            <table class="min-w-full divide-y divide-black/10 text-sm">
                <thead class="bg-black/[0.03] text-left text-xs uppercase text-black/60">
                    <tr>
                        <th class="px-4 py-3"><a href="{{ $sortUrl('name') }}">Membre</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('moving_time') }}">Temps</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('energy_blocks') }}">Blocs d'&eacute;nergie</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('activities_count') }}">Activit&eacute;s</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('distance') }}">Distance</a></th>
                        <th class="px-4 py-3 text-right"><a href="{{ $sortUrl('elevation') }}">&Eacute;l&eacute;vation</a></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    @forelse ($members as $member)
                        <tr class="{{ $member['id'] === $currentUserId ? 'bg-lake/10' : '' }}">
                            <td class="px-4 py-3 font-semibold text-asphalt">
                                <div class="flex items-center gap-3">
                                    @if ($member['profile'])
                                        <img src="{{ $member['profile'] }}" alt="" class="size-9 rounded object-cover" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                        <span class="hidden grid size-9 place-items-center rounded bg-black/10 text-xs font-bold text-black/60" aria-hidden="true">{{ $initials($member['name']) }}</span>
                                    @else
                                        <span class="grid size-9 place-items-center rounded bg-black/10 text-xs font-bold text-black/60" aria-hidden="true">{{ $initials($member['name']) }}</span>
                                    @endif
                                    {{ $member['name'] }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $formatDuration($member['moving_time']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="energy-block-pill">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="energy-block-icon" aria-hidden="true">
                                        <path d="M13 2l-9 12h7l-1 8l10 -13h-7l0 -7z" />
                                    </svg>
                                    <span>{{ number_format($member['energy_blocks'], 1, ',', ' ') }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['activities_count'], 0, ',', ' ') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['distance'] / 1000, 1, ',', ' ') }} km</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($member['elevation'], 0, ',', ' ') }} m</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-black/55">Aucune activit&eacute; synchronis&eacute;e pour cette p&eacute;riode.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
