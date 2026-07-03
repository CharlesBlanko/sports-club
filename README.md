# Blanko Club

Application Laravel pour suivre les activités des membres d'un club Strava.

## Stack

- Laravel `^13.0`
- Tailwind CSS `^4.3` compile avec Webpack/Laravel Mix
- PHP 8.3+
- DDEV, MariaDB 10.11, Node 22

## Démarrage local

```bash
ddev start
ddev composer install
ddev npm install
cp .env.example .env
ddev artisan key:generate
ddev artisan migrate
ddev npm run dev
```

Configurez ensuite les variables Strava dans `.env`:

```dotenv
STRAVA_CLIENT_ID=
STRAVA_CLIENT_SECRET=
STRAVA_REDIRECT_URI=https://blanko-club.ddev.site/auth/strava/callback
STRAVA_CLUB_ID=
```

Dans l'application Strava, l'URL de callback doit correspondre exactement à `STRAVA_REDIRECT_URI`.

## Fonctionnement

- Connexion par OAuth Strava avec les scopes `read,activity:read_all`.
- Refus de connexion si l'athlète n'est pas membre du club `STRAVA_CLUB_ID`.
- Synchronisation complète des activités de l'utilisateur connecté dans la table commune `activities`.
- Calendrier mensuel avec types d'activités et tooltip des membres.
- Tableau de groupe filtrable et triable.
- Liste des 15 dernières activités du club.

## Commandes utiles

```bash
ddev artisan test
ddev artisan migrate:fresh
ddev npm run build
```


## Assets frontend

Le projet compile Tailwind et JavaScript avec Webpack/Laravel Mix. Il n'y a pas de serveur Vite à garder ouvert.

En développement:

```bash
ddev npm install
ddev npm run dev
```

Pour surveiller les changements:

```bash
ddev npm run watch
```

Pour générer les assets de production:

```bash
ddev npm run build
```
