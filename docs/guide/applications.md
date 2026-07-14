# Applications

An application is the container for everything the framework does: config,
aliases, params and components. A WordPress install can run several
applications at once — typically one for a plugin and one for the theme —
each fully isolated with its own config, aliases, router and components.

## Defining an application

```php
use wpmvc\App;

class Theme extends App {

    public static $app;

}
```

::: warning Redeclare `public static $app;`
Every application subclass must redeclare the `$app` property. Without it,
all application classes share (and overwrite) the same static slot.
`init()` warns via `_doing_it_wrong()` when the redeclaration is missing.
:::

## Initialization

```php
$config = require __DIR__ . '/config/main.php';

( new Theme( $config ) )->init();
```

The config array supports these keys:

| Key          | Purpose                                                        |
| ------------ | -------------------------------------------------------------- |
| `name`       | Application name                                                |
| `domain`     | Translation text domain used by `App::t()`                      |
| `aliases`    | Application-specific path/URL aliases                           |
| `components` | Component definitions, keyed by component ID                    |
| `bootstrap`  | Component IDs to instantiate eagerly during `init()`            |
| `options`    | Admin options pages configuration                               |
| `params`     | Arbitrary application parameters (`Theme::$app->params`)        |

## Accessing the instance

```php
Theme::$app;    // the instance, or null before init
Theme::app();   // the instance — never null
```

`Theme::$app` and `Theme::app()` return the same instance. The difference:
`app()` never returns `null` — calling it before the application is
instantiated throws `wpmvc\exceptions\App_Not_Initialized_Exception`.

When `app()` is called on a class with no registered instance of its own
(e.g. the base `App::app()` from shared code), it falls back to the **most
recently initialized** application — the theme, since plugins load first.
The same fallback rule applies to `App::alias()` and the static
`Router::get()/post()/any()` methods.
