---
layout: home
title: WPMVC
titleTemplate: ':title — WordPress MVC Framework'

hero:
  text: Super-light MVC for WordPress
  tagline: A tiny core built entirely on components — declared in config, loaded only when you use them. Models, routing, views, meta boxes and options without the boilerplate.
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/devstudio-rs/wpmvc

features:
  - title: Super-light core
    details: The core is a thin application container and nothing more. No base classes to fight, no overhead on requests that don't use the framework.
  - title: Built on components
    details: Everything — request, router, view, options, logger, user — is a component. Declared in config, lazy-loaded on first access, replaceable with your own class in one line.
  - title: Models over post types
    details: Declare a public property on your model and it becomes post meta — read and written automatically. Chainable query API included.
  - title: Real routing
    details: Map URL paths to controller actions, filtered by HTTP method, with required path parameters — Router::get('user/{id}', ...).
  - title: Meta boxes without ceremony
    details: Declare meta boxes on the model; rendering, nonces, validation, saving and admin notices are wired for you.
  - title: Plugin and theme, side by side
    details: Each application runs isolated with its own config, aliases, router and components. No shared state.
---

<div class="coming-soon">

<div class="coming-soon-header">

<div class="coming-soon-intro">

<span class="coming-soon-badge">In active development</span>

## WPMVC Debugger — coming soon {#wpmvc-debug}

We're actively building **WPMVC Debug**, a debug toolbar and profiler for the
framework. A floating panel on the front end that lets you inspect a request as
you develop — applications, components, database queries, logs, scheduled cron
jobs and the server environment — all without touching your application code.

</div>

<div class="coming-soon-actions">

[Read more →](/guide/debug)

</div>

</div>

[![WPMVC Debug overview panel](/debug/overview.png)](/guide/debug)

</div>

