---
title: Introduction
metadata:
  role: Caching
  group: foundations
  eyebrow: "Eloquent · Model Caching · Per-user Isolation"
  desc: "Attach cached values to Eloquent models, with each user's data kept in its own namespace."
  lead: "Cache values on a model or a single record, like a video's playback position, with each user's values kept separate."
  requires: "PHP ^8.2"
  laravel: "12.x / 13.x"
  licence: MIT
  used_by:
    name: Stry
    desc: "A self-hosted video streaming app."
    href: "https://github.com/francoism90/stry"
---

# Introduction

Laravel Model Cache lets you store small values on your Eloquent models, like where a user stopped watching a video. You can cache a value for one record, or for the whole model class. It works with any Laravel cache store.

Each logged-in user gets their own cache namespace by default. Two users never see each other's value for the same model.

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;

class Video extends Model
{
    use InteractsWithModelCache;
}

$video->modelCache('playback_position', 142);

$video->modelCached('playback_position'); // 142, or null when nothing is cached
```

## Features

- Cache values on a single record, or on the whole model class.
- Keep each user's values separate, automatically.
- Set how long a value lasts, per value or for everything.
- Fetch a value, or compute and store it in one call with `modelCacheRemember()`.
- Use any cache store from `config/cache.php`.

## Installation

```bash
composer require foxws/laravel-modelcache
```

## Learn more

- [Installation](installation.md): the config file and environment variables.
- [Usage](usage.md): every method, for records and for classes.
- [Facade](facade.md): work with the cache outside a model, for example in an action class.
- [Customization](customization.md): choose which values get cached, and how they're kept apart.
