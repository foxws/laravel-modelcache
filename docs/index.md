---
title: Introduction
metadata:
  role: Caching
  group: foundations
  eyebrow: "Eloquent · Model Caching · Per-user Isolation"
  desc: "Attach cached values to Eloquent models, with each user's data kept in its own namespace."
  requires: "PHP ^8.2"
  laravel: "12.x / 13.x"
  licence: MIT
---

# Introduction

Laravel Model Cache lets you attach cached values to Eloquent models — either to one record or to the whole class — using any Laravel cache driver. Each logged-in user gets their own cache namespace by default, so two users never see each other's cached value for the same model.

> [!TIP]
> Let your AI agent do the hard parts. This package ships a [Laravel Boost](https://laravel.com/docs/boost) skill, `modelcache-development`, that teaches your agent how to install, configure and use Laravel Model Cache. Install Boost and select the skill when prompted:
>
> ```bash
> composer require laravel/boost --dev
> php artisan boost:install
> ```
>
> Already using Boost? Run `php artisan boost:update --discover` to pick up the skill. Then just ask, for example: *"Remember each user's playback position on my Video model."*

Continue to [Installation](./installation.md) to get started.
