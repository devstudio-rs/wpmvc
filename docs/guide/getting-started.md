# Getting Started

WPMVC is a super-light MVC framework for WordPress, built entirely on
components. The core is a thin application container; everything else —
routing, views, meta boxes, admin options, logging, the current user — is
a component, declared in config and loaded only on first access. If you
don't use it, it doesn't load.

## Requirements

- PHP 7.2+
- WordPress 6.2+
- [Composer](https://getcomposer.org/)

## Installation

The framework is installed per application — each plugin or theme that
uses WPMVC requires it through its own `composer.json`. For a theme:

```bash
cd wp-content/themes/my-theme
composer require devstudio-rs/wpmvc
```

## Example: a theme on WPMVC

A minimal theme structure:

```
my-theme/
├── composer.json
├── functions.php
├── Theme.php
├── config/
│   └── main.php
├── controllers/
│   └── Site_Controller.php
├── models/
├── views/
├── index.php
└── style.css
```

### composer.json

Besides requiring the framework, register the theme's own namespace so
controllers and models autoload:

```json
{
    "require": {
        "devstudio-rs/wpmvc": "^1.6"
    },
    "autoload": {
        "psr-4": {
            "theme\\": "."
        }
    }
}
```

Run `composer install` (or the `composer require` above) to generate the
autoloader.

### Theme.php

Create the application class by extending `\wpmvc\App`. Every application
class **must redeclare** `public static $app;` — that gives it its own
static slot, so multiple applications (e.g. a plugin and a theme) can run
side by side without overwriting each other:

```php
<?php

namespace theme;

use wpmvc\App;

class Theme extends App {

    public static $app;

}
```

### config/main.php

```php
<?php

return array(
    'name'   => 'Theme',
    'domain' => 'theme',

    'aliases' => array(
        '@root' => get_template_directory(),
        '@web'  => get_template_directory_uri(),
    ),
);
```

### functions.php

Load the autoloader, initialize the application and register routes:

```php
<?php

namespace theme;

use theme\controllers\Site_Controller;
use wpmvc\web\Router;

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config/main.php';

( new Theme( $config ) )->init();

Router::get( 'site', array( Site_Controller::class, 'action_index' ) );
```

### controllers/Site_Controller.php

```php
<?php

namespace theme\controllers;

use wpmvc\web\Controller;

class Site_Controller extends Controller {

    public function action_index() {
        echo 'Hello from WPMVC.';
    }

}
```

Activate the theme and open `{host}/site/` — the action responds.

From here, the application and its components are available anywhere:

```php
Theme::$app->request->post();
Theme::$app->user->is_guest;
Theme::$app->logger->log( 'Hello', Logger::TYPE_INFO );
```

## Next steps

- [Applications](/guide/applications) — lifecycle, config, multiple apps
- [Routing](/guide/routing) — HTTP methods and route parameters
- [Post Models](/guide/models) — custom post types with meta attributes
- [Meta Boxes](/guide/meta-boxes) — admin UI without the ceremony
