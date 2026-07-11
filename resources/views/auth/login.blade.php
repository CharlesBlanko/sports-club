@extends('layouts.app')

@section('content')
    <section class="auth-login-panel mx-auto rounded border border-black/10 bg-white p-8 text-center">
        <p class="mb-2 text-sm font-bold uppercase text-clay">Connexion requise</p>
        <h1 class="mb-4 text-3xl font-black text-asphalt">Connectez-vous avec Strava</h1>
        <p class="mb-6 text-sm text-black/65">
            Pour consulter les activit&eacute;s du club, vous devez d'abord vous connecter avec votre compte Strava.
        </p>
        <div class="flex justify-center">
            <a href="{{ route('strava.redirect') }}" class="inline-block rounded-md transition-opacity hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-clay focus:ring-offset-2" aria-label="Se connecter avec Strava">
                <img src="{{ asset('images/btn_strava_connect_with_orange.svg') }}" width="237" height="48" alt="Connect with Strava">
            </a>
        </div>
    </section>
@endsection
