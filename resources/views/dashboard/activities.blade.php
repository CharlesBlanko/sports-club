@extends('layouts.app')

@php
    $formatDuration = function (int $seconds) {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        return $hours.' h '.str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    };
@endphp

@section('content')
    @include('dashboard.partials.heading', [
        'eyebrow' => 'Journal',
        'title' => 'Activit&eacute;s',
    ])

    <section>
        <h2 class="mb-4 text-xl font-bold text-asphalt">Activit&eacute;s</h2>
        <div class="grid gap-3">
            @forelse ($latestActivities as $activity)
                <article class="rounded border border-black/10 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-asphalt">{{ $activity->name }}</h3>
                            <p class="text-sm text-black/60">{{ $activity->user->name }} &middot; {{ $activity->started_at->translatedFormat('j F Y, H:i') }}</p>
                        </div>
                        <span class="rounded bg-black/[0.06] px-3 py-1 text-xs font-bold uppercase text-black/65">{!! $activity->sport_label !!}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                        <div><span class="block text-xs uppercase text-black/50">Distance</span><strong>{{ number_format($activity->distance / 1000, 1, ',', ' ') }} km</strong></div>
                        <div><span class="block text-xs uppercase text-black/50">Temps</span><strong>{{ $formatDuration($activity->moving_time) }}</strong></div>
                        <div><span class="block text-xs uppercase text-black/50">&Eacute;l&eacute;vation</span><strong>{{ number_format($activity->total_elevation_gain, 0, ',', ' ') }} m</strong></div>
                    </div>
                </article>
            @empty
                <div class="rounded border border-black/10 bg-white p-8 text-center text-black/55">Les derni&egrave;res activit&eacute;s appara&icirc;tront ici apr&egrave;s la premi&egrave;re synchronisation.</div>
            @endforelse
        </div>
    </section>
@endsection
