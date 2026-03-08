# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Cupidon** is a Symfony 7.3 couples' intimacy card game application. Players are shown a sequence of positions (with images and descriptions) in progressive intensity order, accompanied by a countdown timer.

## Common Commands

```bash
# Install dependencies
symfony composer install

# Database setup
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load   # dev only

# Run the dev server (requires Symfony CLI)
symfony server:start

# Run tests
php bin/phpunit

# Run a single test file
php bin/phpunit tests/path/to/TestFile.php

# Clear cache
symfony console cache:clear

# Create a new migration after entity changes
symfony console doctrine:migrations:diff
```

## Architecture

### Core Game Logic

The game is **not stored in Doctrine** — it lives in the PHP session as a serialized array. The flow is:

1. `GameInitializer::quickFireGameInitialize()` creates a `Game` model, sets intensity quotas, filters compatible positions, and shuffles them.
2. `Game::toArray()` / `Game::fromArray()` serialize/deserialize state for session storage (repositories are not serializable so they're re-injected on deserialization).
3. `QuickFireGame` (Live Component) reads/writes the game from session on every action (`next`, `reset`) and uses `drawNextPosition()` to advance through the deck.

### Intensity Progression

`IntensityEnum` defines the ordered intensity levels: `WARMUP → DESIRE → SPARK → FIRE → ERUPTION`. Positions are drawn in this order. The quota per intensity is currently hardcoded in `GameInitializer`.

### Position Filtering

A position is included in the game deck only if **all** of its required accessories are in the player's selected accessories list, OR if it has no accessories at all (`accessoriesAreCompatible()`). Accessory selection is not yet wired to a form (TODO in `GameInitializer`).

### Frontend Stack

- **Tailwind CSS** via `symfonycasts/tailwind-bundle` (no Node build step — processed by PHP)
- **Stimulus JS** for client-side behavior (countdown timer in `assets/controllers/counter_controller.js`)
- **Symfony UX Live Components** for reactive server-side rendering (`#[AsLiveComponent]`, `#[LiveAction]`, `#[LiveListener]`)
- **Symfony UX Turbo** for SPA-like navigation

### Live Component Events

The `Countdown` and `QuickFireGame` components communicate via browser events:
- `countdownUpdate` — dispatched by `QuickFireGame` to reset the countdown on new position
- `removeCountdown` — dispatched when no more positions remain
- `countdownASEnded` — listened to by `QuickFireGame` to auto-advance on timer expiry

### Admin Backend

EasyAdmin at `/admin` (redirects to `/admin/accessories`). Manages `Accessories` and `Position` entities including image uploads via VichUploader.

### Database

MySQL (`cupidon` database, see `.env`). Docker Compose defines a Postgres service as alternative but the `.env` defaults to MySQL on localhost.

### Key Files

| File | Purpose |
|------|---------|
| `src/Model/Game.php` | Core game state model (session-serialized) |
| `src/Service/GameInitializer.php` | Game factory — sets quotas and initializes deck |
| `src/Twig/Components/QuickFireGame.php` | Live Component — game controller |
| `src/Twig/Components/Countdown.php` | Live Component — timer |
| `src/Enum/IntensityEnum.php` | Intensity levels with translation support |
| `assets/controllers/counter_controller.js` | Stimulus countdown timer |
| `templates/components/QuickFireGame.html.twig` | Game UI template |
