---
title: Introduction
metadata:
  role: Caching
  eyebrow: "Eloquent · Model Caching · Per-user Isolation"
  desc: "Attach cached values to Eloquent models, with each user's data kept in its own namespace."
  requires: "PHP ^8.2"
  laravel: "12.x / 13.x"
  licence: MIT
---

# Introduction

Laravel Model Cache lets you attach cached values to Eloquent models — either to one record or to the whole class — using any Laravel cache driver. Each logged-in user gets their own cache namespace by default, so two users never see each other's cached value for the same model.

Continue to [Installation](./installation.md) to get started.
