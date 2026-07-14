# wpmvc

## App

Create your application class by extending `\wpmvc\App`. Every application
class **must redeclare** `public static $app;` — that gives it its own static
slot, so multiple applications (e.g. a plugin and a theme) can run side by
side without overwriting each other. `init()` warns via `_doing_it_wrong()`
if the redeclaration is missing.

```php
use wpmvc\App;

class Theme extends App {

    public static $app;

}
```

Initialize the application:

```php
$config = array(
    'aliases' => array(
        '@root' => get_template_directory(),
        '@web'  => get_template_directory_uri(),
    ),
    'components' => array(
        'logger' => array(
            'class'     => \wpmvc\components\Logger::class,
            'directory' => '@upload.basedir/logs',
        ),
    ),
);

( new Theme( $config ) )->init();
```

The application and its components are then available:

```php
Theme::$app->request->post();
Theme::app()->request->post();
```

`Theme::$app` and `Theme::app()` return the same instance. The difference:
`app()` never returns `null` — calling it before the application is
instantiated throws `wpmvc\exceptions\App_Not_Initialized_Exception`,
while `Theme::$app` would simply be `null` at that point.

Each application has its own config, aliases, router and components. A
plugin and a theme running wpmvc at the same time do not share any state.

### Components

Components are declared under the `components` config key and are
**lazy-loaded** — a component is instantiated on first access
(`Theme::$app->logger`), not upfront.

Some components need to register WordPress hooks even if they are never
accessed directly (e.g. `view`, which enqueues assets from its constructor).
List those under the `bootstrap` config key to instantiate them eagerly
during `init()`:

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

### Aliases

Framework aliases are available in every application: `@wpmvc.path`,
`@wpmvc.url`, `@home`, `@content`, `@upload.basedir`, `@upload.baseurl`,
`@upload.dir`, `@upload.url`. Application-specific aliases (`@root`, `@web`,
custom ones) are defined under the `aliases` config key.

```php
Theme::alias( '@web/assets/main.css' );
```

Aliases used inside a component's config are resolved automatically by the
owning application when the component is instantiated — components receive
final values and hold no reference to the application.

Rule of thumb: application-specific aliases must be passed via config;
class-level property defaults may only use framework aliases, which are
identical in every application.

## Models

### Post Models

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';

    // Example of custom attributes with default values.
    // Attributes not present on WP_Post are stored as post meta.
    public $event_date     = 1707422767;
    public $event_location = 'Bratislava';

    protected function registry() : array {
        return array(
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            ...
        );
    }

    protected function registry_labels() : array {
        return array(
            'name' => __( 'Event' ),
            ...
        );
    }

}
```

Ways to use models. Set attributes, save or delete.

```php
$event = Event::find_one( 24 );
$event = new Event();

$event->set_attribute( 'post_title', 'Great Event' );
$event->set_attribute( 'event_location', 'Bratislava' );

$event->set_attributes( array(
    'post_title'     => 'Great Event',
    'event_location' => 'Bratislava',
) );

$event->load( array(
    'Event' => array(
        'post_title'     => 'Great Event',
        'event_location' => 'Bratislava',
    ),
) );

$event->save();
$event->delete();
```

#### Register custom post type with post model

Hook the model's static `register()` to the `init` WordPress action.
`register()` takes no arguments — the post type args come from the model's
`registry()`, `registry_labels()`, `registry_supports()` and
`registry_rewrite()` methods:

```php
add_action( 'init', array( Event::class, 'register' ) );
```

### Taxonomy Models

```php
class Event_Category extends \wpmvc\models\Taxonomy_Model {

    public $taxonomy = 'event-category';

    public function registry() : array {
        return array(
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
        );
    }

    public function registry_labels() : array {
        return array(
            'name' => __( 'Event Category' ),
            ...
        );
    }

    public function registry_object_type() : array {
        return array( 'event' );
    }

}
```

Taxonomies are registered the same way as post types:

```php
add_action( 'init', array( Event_Category::class, 'register' ) );
```

Search posts by taxonomy.

```php
$events = Event::find()
    ->where_taxonomy( Event_Category::class, array( 'slug' => 'taxonomy_slug' ) )
    ->all();
```

## Controllers

```php
class Site_Controller extends \wpmvc\web\Controller {

    public function action_index() {
        // Action logic.
    }

}
```

Register a route on your application's router, after `init()`:

```php
Theme::$app->router->add_route( 'site', array( Site_Controller::class, 'action_index' ) );
```

The registered action will be available at `{host}/site/`. Each application
has its own router — routes are always registered explicitly on the
application that should handle them.

## Meta boxes

Define a meta box controller. `on_action()` renders the meta box,
`on_save()` persists it:

```php
class Event_Options_Controller extends \wpmvc\web\Meta_Box_Controller {

    public function on_action( $model ) {
        echo View::render( 'views/events/meta-box', array(
            'model' => $model,
        ) );
    }

    public function on_save( $model ) {
        if ( $model->load( Theme::$app->request->post() ) ) {
            $model->save();
        }
    }

}
```

Add the meta box to the post model by passing the controller class and a
title to `add_meta_box()`:

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';

    protected function init() {
        $this->add_meta_box( Event_Options_Controller::class, __( 'Options' ) );
    }

}
```

`init()` runs right after the post type is registered, so hooking
`add_action( 'init', array( Event::class, 'register' ) )` is all that is
needed to get both the post type and its meta boxes.
