# Components

Components are the application's building blocks: `request`, `router`,
`view`, `options`, `logger`, `user`… They are declared under the
`components` config key and **lazy-loaded** — a component is instantiated
on first access, not upfront:

```php
$config = array(
    'components' => array(
        'logger' => array(
            'class'     => \wpmvc\components\Logger::class,
            'directory' => '@upload.basedir/logs',
        ),
    ),
);

// Instantiated here, on first access:
Theme::$app->logger->log( 'Hello', Logger::TYPE_INFO );
```

Everything in the definition besides `class` is set as a property on the
component instance. Aliases in the definition are resolved by the owning
application before the component is constructed — components receive final
values and hold no reference to the application.

## Built-in components

Every application provides these out of the box, no config needed:

| ID        | Class                       | Purpose                          |
| --------- | --------------------------- | -------------------------------- |
| `request` | `wpmvc\web\Request`         | GET/POST input, request method   |
| `router`  | `wpmvc\web\Router`          | Route registry                   |
| `user`    | `wpmvc\components\User`     | Currently logged-in user         |

Definitions in your config are merged over the defaults, so you can
override any built-in (e.g. swap the `user` component for a subclass).

## Bootstrap (eager loading)

Some components must register WordPress hooks even if they are never
accessed directly — e.g. `view`, which enqueues assets from its
constructor. List those under the `bootstrap` key to instantiate them
eagerly during `init()`:

```php
$config = array(
    'bootstrap' => array( 'view' ),

    'components' => array(
        'view' => array(
            'class'  => \wpmvc\web\View::class,
            'assets' => require __DIR__ . '/assets.php',
        ),
    ),
);
```

## Writing a component

Extend `\wpmvc\base\Component`. The constructor applies the config as
attributes; declare defaults as public properties:

```php
class Mailer extends \wpmvc\base\Component {

    public $from = 'noreply@example.com';

    public function send( $to, $subject, $message ) {
        return wp_mail( $to, $subject, $message, array(
            'From: ' . $this->from,
        ) );
    }

}
```

```php
'components' => array(
    'mailer' => array(
        'class' => Mailer::class,
        'from'  => 'hello@example.com',
    ),
),
```

```php
Theme::$app->mailer->send( ... );
```
