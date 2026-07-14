# Changelog

Versioning during active 1.x development: **patch** for bug fixes and
internal refactors, **minor** for new features *and* breaking API changes
(spelled out in the release notes). `2.0.0` is reserved for the
stabilization milestone; from then on breaking changes bump major per
semver.

## 1.6.0

**Plugin views.** `View::render()` accepts absolute paths: a relative view
keeps the `get_template_part()` flow (child-theme overrides intact), while
an absolute path — e.g. `WPMVC::alias( '@root/views/test' )` or
`__DIR__ . '/views/test'` — loads the file directly via `load_template()`,
so `$params` behave identically. The `.php` extension is appended when
missing; a non-existent absolute view renders nothing.

## 1.5.0

**Route path parameters.** Route paths can declare `{param}` placeholders,
matched as exactly one URL segment and passed to the action as positional
arguments in declaration order. Parameters are required on both levels: a
placeholder route only matches complete URLs, and a route providing fewer
values than the action's required parameters is skipped (regular 404)
instead of fataling.

## 1.4.0

**Static Router API + HTTP method matching.** Routes can be registered via
`Router::get()` / `Router::post()` / `Router::any()` and are matched by
HTTP method on `template_redirect`. The static form registers on the most
recently initialized application's router (same fallback rule as
`App::alias()`); `add_route()` gains an optional `$method` argument
(defaults to `METHOD_ANY`) and stays fully backward compatible. Also adds
`Request::method()`.

## 1.3.0

**User component.** Built-in `user` component wrapping the currently
logged-in WordPress user, registered as a default component on every
application — `is_guest`, `id`, `identity`, `role`, `roles`,
`has_role()`, `can()`. State is resolved live from WordPress on every
call.

## 1.2.0 and earlier

See the [GitHub releases](https://github.com/devstudio-rs/wpmvc/releases)
and commit history.
