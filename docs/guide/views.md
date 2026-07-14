# Views

`View::render( $view, $params )` renders a view file and returns its
output; `$params` are available as variables inside the template:

```php
use wpmvc\base\View;

echo View::render( 'views/test', array( 'message' => 'Hello' ) );
```

```php
<!-- views/test.php -->
<p><?php echo esc_html( $message ); ?></p>
```

## Theme views

A **relative** view resolves through `get_template_part()` — the standard
theme flow, child-theme overrides included:

```php
View::render( 'views/events/options' );
// -> {theme}/views/events/options.php
```

## Plugin views

An **absolute** path loads that file directly, which is how plugins render
their own views:

```php
View::render( WPMVC::alias( '@root/views/test' ) );  // via the @root alias
View::render( __DIR__ . '/views/test' );             // relative to the current file
```

The `.php` extension is appended when missing. A non-existent absolute
view renders nothing — the same silent behavior as `get_template_part()`.

::: warning Resolve @root through your own application class
Use `WPMVC::alias( '@root/...' )` (your application class), not the base
`App::alias()` — `@root` is application-specific and the static fallback
would otherwise resolve against the most recently initialized application.
:::

## Assets

Assets are defined in the view component's config as arrays of
`handle` / `src` / `deps`, using the `@web` alias for app-relative URLs:

```php
// config/assets.php
return array(
    'css' => array(
        array(
            'handle' => 'theme-main',
            'src'    => '@web/assets/css/main.css',
        ),
    ),
    'js' => array(
        array(
            'handle' => 'theme-main',
            'src'    => '@web/assets/js/main.js',
            'deps'   => array( 'jquery' ),
        ),
    ),
);
```

Wire it up via the `view` component (bootstrapped, so its hooks register
eagerly):

```php
'bootstrap' => array( 'view' ),

'components' => array(
    'view' => array(
        'class'  => \wpmvc\web\View::class,
        'assets' => require __DIR__ . '/assets.php',
    ),
),
```

Asset URLs are automatically versioned with the file's modification time,
so browser caches bust on every change.
