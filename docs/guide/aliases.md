# Aliases

Aliases are shorthand tokens for paths and URLs, resolved per application:

```php
Theme::alias( '@web/assets/main.css' );
// -> https://example.com/wp-content/themes/my-theme/assets/main.css
```

## Framework aliases

Available in **every** application, identical everywhere:

| Alias             | Resolves to                          |
| ----------------- | ------------------------------------ |
| `@wpmvc.path`     | Framework directory path             |
| `@wpmvc.url`      | Framework directory URL              |
| `@home`           | `home_url()`                         |
| `@content`        | `content_url()`                      |
| `@upload.basedir` | Uploads base directory               |
| `@upload.baseurl` | Uploads base URL                     |
| `@upload.dir`     | Current upload directory             |
| `@upload.url`     | Current upload URL                   |

## Application aliases

Application-specific aliases — `@root`, `@web` and any custom ones — are
defined under the `aliases` config key:

```php
'aliases' => array(
    '@root' => get_template_directory(),
    '@web'  => get_template_directory_uri(),
),
```

## Resolution rules

Aliases used inside a component's config are resolved automatically by the
owning application when the component is instantiated — components receive
final values and hold no reference to the application.

Calling `alias()` statically resolves against the called class's
application: `Theme::alias()` uses the theme's aliases, `WPMVC::alias()`
the plugin's. Called on a class with no instance of its own (the base
`App::alias()`), it falls back to the most recently initialized
application.

::: warning Rule of thumb
Application-specific aliases (`@root`, `@web`, custom) must be passed via
config or resolved through the owning application class. Class-level
property defaults may only use framework aliases, which are identical in
every application.
:::
