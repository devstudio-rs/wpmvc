---
layout: home

hero:
  name: WPMVC
  text: MVC framework for WordPress
  tagline: Models, routing, views, meta boxes and options — the WordPress way, without the boilerplate. Inspired by Yii2.
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/devstudio-rs/wpmvc

features:
  - title: Light core, lazy components
    details: Components are declared in config and instantiated on first access. Nothing loads until you use it.
  - title: Models over post types
    details: Declare a public property on your model and it becomes post meta — read and written automatically. Chainable query API included.
  - title: Real routing
    details: Map URL paths to controller actions, filtered by HTTP method, with required path parameters — Router::get('user/{id}', ...).
  - title: Meta boxes without ceremony
    details: Declare meta boxes on the model; rendering, nonces, validation, saving and admin notices are wired for you.
  - title: Plugin and theme, side by side
    details: Each application runs isolated with its own config, aliases, router and components. No shared state.
  - title: Batteries included
    details: Current-user component, admin options pages, logger with rotation, form helpers and asset cache busting.
---
