<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Blanko Club') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="min-h-screen font-sans antialiased">
    <header class="border-b border-black/10 bg-white/85 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded bg-clay text-lg font-black text-white">BC</span>
                <span>
                    <span class="block text-lg font-bold tracking-tight">{{ config('app.name') }}</span>
                    <span class="block text-xs uppercase text-black/55">Club Strava</span>
                </span>
            </a>

            <div class="flex items-center gap-3">
                @auth
                    <form method="post" action="{{ route('sync') }}">
                        @csrf
                        <button class="rounded bg-lake px-4 py-2 text-sm font-semibold text-white transition hover:bg-asphalt">Synchroniser</button>
                    </form>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded border border-black/15 px-4 py-2 text-sm font-semibold text-asphalt transition hover:border-clay hover:text-clay">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('strava.redirect') }}" class="rounded bg-clay px-4 py-2 text-sm font-semibold text-white transition hover:bg-asphalt">Connexion Strava</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded border border-moss/30 bg-moss/10 px-4 py-3 text-sm font-medium text-moss">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded border border-clay/30 bg-clay/10 px-4 py-3 text-sm font-medium text-clay">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
