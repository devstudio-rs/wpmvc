# Controllers

Controllers hold the actions that routes dispatch to:

```php
use theme\Theme;
use wpmvc\web\Controller;

class Site_Controller extends Controller {

    public function action_index() {
        $model = new Event();

        $model->load( Theme::$app->request->post() );
        $model->validate();
        $model->to_response();
    }

}
```

Route the action (see [Routing](/guide/routing) for the full picture):

```php
Router::post( 'site', array( Site_Controller::class, 'action_index' ) );
```

## Request input

Access GET/POST input through the application's `request` component:

```php
Theme::$app->request->get();               // all GET params
Theme::$app->request->get( 'id' );         // single param
Theme::$app->request->get( 'id', 10 );     // with default
Theme::$app->request->post();              // all POST params
Theme::$app->request->post( 'title' );     // single param
Theme::$app->request->method();            // 'GET', 'POST', ...
```

## Route parameters

`{param}` placeholders from the route path arrive as positional action
arguments:

```php
Router::get( 'user/{action}/{id}', array( User_Controller::class, 'action_show' ) );

class User_Controller extends Controller {

    public function action_show( $action, $id ) {
        // /user/edit/5/ -> $action = 'edit', $id = '5'
    }

}
```

## Lifecycle

When a route matches, the framework instantiates the controller, sets
`$controller->action`, calls `before_action()` and then invokes the
action. Override `before_action()` for checks shared by all actions of a
controller (authentication, capability checks, and similar).
