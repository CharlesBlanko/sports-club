@extends('layouts.app')

@section('content')
    <section class="auth-login-panel mx-auto rounded border border-black/10 bg-white p-8 text-center">
        <p class="mb-2 text-sm font-bold uppercase text-clay">Connexion requise</p>
        <h1 class="mb-4 text-3xl font-black text-asphalt">Connectez-vous avec Strava</h1>
        <p class="mb-6 text-sm text-black/65">
            Pour consulter les activit&eacute;s du club, vous devez d'abord vous connecter avec votre compte Strava.
        </p>
        <a href="{{ route('strava.redirect') }}" class="auth-login-button rounded bg-clay text-sm font-semibold text-white transition hover:bg-asphalt">
            Connexion Strava
        </a>
    </section>
@endsection
