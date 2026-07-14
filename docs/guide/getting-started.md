# Getting Started

WPMVC is an MVC framework built on top of WordPress. It simplifies the
WordPress functionality you touch every day — custom post types, routing,
views, meta boxes, admin options, logging — behind a small, config-driven
core inspired by Yii2.

## Requirements

- PHP 7.2+
- WordPress 6.2+

## Installation

The framework lives in its own directory (e.g. `wpmvc/` in the WordPress
root) and is autoloaded under the `wpmvc\` namespace (PSR-4 from
`wpmvc/src/`). Require its autoloader from your plugin or theme:

```php
require_once ABSPATH . 'wpmvc/vendor/autoload.php';
```

## Your first application

Create an application class by extending `\wpmvc\App`. Every application
class **must redeclare** `public static $app;` — that gives it its own
static slot, so multiple applications (e.g. a plugin and a theme) can run
side by side without overwriting each other:

```php
use wpmvc\App;

class Theme extends App {

    public static $app;

}
```

Initialize it with a config array:

```php
$config = array(
    'aliases' => array(
        '@root' => get_template_directory(),
        '@web'  => get_template_directory_uri(),
    ),
);

( new Theme( $config ) )->init();
```

From that point the application and its components are available anywhere:

```php
Theme::$app->request->post();
Theme::app()->request->post();
```

Continue with [Applications](/guide/applications) to understand the
application lifecycle, or jump straight to [Routing](/guide/routing) and
[Post Models](/guide/models).
