<?php

namespace wpmvc\base;

abstract class Router extends Component {

    public $routes = array();

    public static function register( $path, $action ) {
        \wpmvc\App::$app->router->add_route( $path, $action );
    }

    public function add_route( $path, $action ) {
        $this->routes[] = array(
            'path'   => $path,
            'action' => $action,
        );
    }

}
