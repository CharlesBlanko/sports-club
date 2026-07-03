<section class="mb-8 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
    <div>
        <p class="text-sm font-semibold uppercase text-clay">{!! $eyebrow !!}</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-asphalt sm:text-4xl">{!! $title !!}</h1>
    </div>
    @auth
        <div class="rounded border border-black/10 bg-white px-4 py-3 text-sm text-black/65">
            Connect&eacute;: <span class="font-semibold text-asphalt">{{ auth()->user()->name }}</span>
            @if (auth()->user()->last_synced_at)
                <span class="block text-xs">Derni&egrave;re synchro: {{ auth()->user()->last_synced_at->diffForHumans() }}</span>
            @endif
        </div>
    @else
        <div class="rounded border border-clay/25 bg-white px-4 py-3 text-sm text-black/65">
            Connectez-vous avec Strava pour synchroniser vos activit&eacute;s.
        </div>
    @endauth
</section>
