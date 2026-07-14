# Routing

Routes map URL paths to controller actions and are matched on
`template_redirect`, filtered by HTTP method.

## Registering routes

Register routes after `init()`. The static `Router::get()`,
`Router::post()` and `Router::any()` methods register a route filtered by
HTTP method:

```php
use wpmvc\web\Router;

Router::get( 'user', array( User_Controller::class, 'action_show' ) );    // GET only
Router::post( 'site', array( Site_Controller::class, 'action_index' ) );  // POST only
Router::any( 'page', array( Page_Controller::class, 'action_index' ) );   // any method
```

The registered action is available at `{host}/user/` etc. A request whose
method does not match falls through to regular WordPress handling.

## Route parameters

Route paths can declare `{param}` placeholders. Each placeholder matches
exactly one URL segment, and the captured values are passed to the action
as positional arguments, in declaration order:

```php
Router::get( 'user/{id}', array( User_Controller::class, 'action_show' ) );
// /user/5/ -> action_show( '5' )

Router::get( 'user/{action}/{id}', array( User_Controller::class, 'action_show' ) );
// /user/edit/5/ -> action_show( 'edit', '5' )
```

### Parameters are required

- A route with placeholders only matches URLs where every segment is
  present — `user/{action}/{id}` does not match `/user/` or `/user/edit/`.
  A bare `/user/` URL works only when registered as its own route.
- A route is never dispatched with missing arguments: when the action
  declares more required parameters than the route provides, the route is
  skipped and WordPress serves its regular 404. Give the parameters
  defaults (`action_show( $id = null )`) when a bare route should reach
  the same action.

::: warning Raw input
Captured values arrive as raw URL strings — sanitize them in the action.
:::

## Routers are per-application

Each application has its own router — there is no global route registry.
The static form registers on the **most recently initialized** application
(the theme, since plugins load first) — the same fallback rule as
`App::alias()`. To target a specific application, use the instance method,
whose optional third argument is the HTTP method (defaults to
`Router::METHOD_ANY`):

```php
WPMVC::$app->router->add_route(
    'core-test',
    array( Test_Controller::class, 'action_view' ),
    Router::METHOD_GET
);
```

## Request method

The HTTP method of the current request is available as:

```php
App::$app->request->method();  // 'GET', 'POST', ...
```
