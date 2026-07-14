<?php

namespace wpmvc\base;

class Router extends Component {

    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_ANY  = '*';

    public $routes = array();

    /**
     * Register a route on this router instance.
     *
     * @param string $path
     * @param array $action [ Controller_Class::class, 'action_name' ]
     * @param string $method One of the METHOD_* constants.
     * @return void
     */
    public function add_route( $path, $action, string $method = self::METHOD_ANY ) {
        $this->routes[] = array(
            'path'   => $path,
            'action' => $action,
            'method' => strtoupper( $method ),
        );
    }

    /**
     * Register a GET route on the current application's router.
     *
     * Like `App::alias()`, the static form resolves against the most
     * recently initialized application (the theme, since plugins load
     * first). To target a specific app, use the instance method:
     * `WPMVC::$app->router->add_route( $path, $action, $method )`.
     *
     * @since 1.4.0
     *
     * @param string $path
     * @param array $action
     * @return void
     */
    public static function get( $path, $action ) {
        static::app_router()->add_route( $path, $action, self::METHOD_GET );
    }

    /**
     * Register a POST route on the current application's router.
     *
     * @since 1.4.0
     *
     * @param string $path
     * @param array $action
     * @return void
     */
    public static function post( $path, $action ) {
        static::app_router()->add_route( $path, $action, self::METHOD_POST );
    }

    /**
     * Register a route matching any HTTP method on the current
     * application's router.
     *
     * @since 1.4.0
     *
     * @param string $path
     * @param array $action
     * @return void
     */
    public static function any( $path, $action ) {
        static::app_router()->add_route( $path, $action, self::METHOD_ANY );
    }

    /**
     * Whether a route accepts the given HTTP request method.
     *
     * @since 1.4.0
     *
     * @param array $route
     * @param string $request_method
     * @return bool
     */
    public function matches_method( array $route, string $request_method ) : bool {
        $method = $route['method'] ?? self::METHOD_ANY;

        return $method === self::METHOD_ANY || $method === strtoupper( $request_method );
    }

    /**
     * Router of the most recently initialized application.
     *
     * @return self
     * @throws \wpmvc\exceptions\App_Not_Initialized_Exception When no application exists yet.
     */
    protected static function app_router() : self {
        return App::app()->router;
    }

}
