<?php

namespace wpmvc\base;

class Router extends Component {

    public $routes = array();

    public function add_route( $path, $action ) {
        $this->routes[] = array(
            'path'   => $path,
            'action' => $action,
        );
    }

}
