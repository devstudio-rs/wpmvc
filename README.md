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

### Routes

Register routes after `init()`. The static `Router::get()`, `Router::post()`
and `Router::any()` methods register a route filtered by HTTP method:

```php
use wpmvc\web\Router;

Router::get( 'user', array( User_Controller::class, 'action_show' ) );    // GET only
Router::post( 'site', array( Site_Controller::class, 'action_index' ) );  // POST only
Router::any( 'page', array( Page_Controller::class, 'action_index' ) );   // any method
```

The registered action will be available at `{host}/site/` etc. A request
whose method does not match falls through to regular WordPress handling.

Each application has its own router — there is no global route registry.
The static form registers on the **most recently initialized** application
(the theme, since plugins load first) — the same fallback rule as
`App::alias()`. To target a specific application, use the instance method,
whose optional third argument is the HTTP method (defaults to
`Router::METHOD_ANY`):

```php
WPMVC::$app->router->add_route( 'site', array( Site_Controller::class, 'action_index' ), Router::METHOD_POST );
```

### Route parameters

Route paths can declare `{param}` placeholders. Each placeholder matches
exactly one URL segment, and the captured values are passed to the action
as positional arguments, in declaration order:

```php
Router::get( 'user/{id}', array( User_Controller::class, 'action_show' ) );
// /user/5/ -> action_show( '5' )

Router::get( 'user/{action}/{id}', array( User_Controller::class, 'action_show' ) );
// /user/edit/5/ -> action_show( 'edit', '5' )
```

Parameters are required:

- A route with placeholders only matches URLs where every segment is
  present — `user/{action}/{id}` does not match `/user/` or `/user/edit/`.
  A bare `/user/` URL works only when registered as its own route.
- A route is never dispatched with missing arguments: when the action
  declares more required parameters than the route provides, the route is
  skipped and WordPress serves its regular 404. Give the parameters
  defaults (`action_show( $id = null )`) when a bare route should reach
  the same action.

Captured values arrive as raw URL strings — sanitize them in the action.
The HTTP method of the current request is available as
`App::$app->request->method()`.

## Views

`View::render( $view, $params )` renders a view file and returns its
output; `$params` are available as variables inside the template:

```php
use wpmvc\base\View;

echo View::render( 'views/test', array( 'message' => 'Hello' ) );
// views/test.php: <p><?php echo esc_html( $message ); ?></p>
```

A **relative** view resolves through `get_template_part()` — the theme
flow, child-theme overrides included. An **absolute** path loads that file
directly, which is how plugins render their own views:

```php
View::render( 'views/test' );                        // theme: views/test.php
View::render( WPMVC::alias( '@root/views/test' ) );  // plugin: {plugin_root}/views/test.php
View::render( __DIR__ . '/views/test' );             // plugin, relative to the current file
```

The `.php` extension is appended when missing. A non-existent absolute
view renders nothing — the same silent behavior as `get_template_part()`.

Note: resolve the alias through your application class
(`WPMVC::alias( '@root/... ' )`), not the base `App::alias()` — `@root`
is application-specific and the static fallback would otherwise resolve
against the most recently initialized application.

## Current user

Every application has a built-in `user` component wrapping the currently
logged-in WordPress user — available out of the box, no configuration
required:

```php
App::$app->user->is_guest;             // bool (also callable: is_guest())
App::$app->user->id;                   // int, 0 for guests
App::$app->user->identity;             // WP_User|null
App::$app->user->role;                 // primary role, null for guests
App::$app->user->roles;                // string[]
App::$app->user->has_role( 'editor' );
App::$app->user->can( 'edit_post', $post_id );  // current_user_can()
```

Properties are read-only virtual attributes resolved via `get_*()` getters.
All state is read live from WordPress (never cached), so it stays correct
even when the current user changes mid-request (`wp_set_current_user()`).
Like any component, it can be overridden per application via the
`components` config key.

## Meta boxes

Define a meta box controller. In the common case only `on_action()`
(render) is needed — the inherited `on_save()` already loads request data
into the model, validates it and saves, showing validation errors as an
admin notice after the redirect:

```php
class Event_Options_Controller extends \wpmvc\web\Meta_Box_Controller {

    public function on_action( $model ) {
        echo View::render( 'views/events/meta-box', array(
            'model' => $model,
        ) );
    }

}
```

Override `on_save( $model )` only when custom persistence logic is needed.

Declare the meta box on the post model — `register()` wires everything
(rendering, nonce, capability checks, saving) automatically:

```php
class Event extends \wpmvc\models\Post_Model {

    public $post_type = 'event';

    protected function meta_boxes() : array {
        return array(
            array( Event_Options_Controller::class, __( 'Options' ) ),
        );
    }

}
```

Each `meta_boxes()` item is `array( Controller_Class::class, $title, $args )`
— title and args are optional. When the title is omitted it is derived from
the controller class name (`Event_Options_Controller` → “Event Options”).
For dynamic cases, `$this->add_meta_box( $controller, $title, $args )` can
still be called from the model's `init()`.

With `add_action( 'init', array( Event::class, 'register' ) )` in place,
the post type and its meta boxes are fully wired.
