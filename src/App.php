<?php

namespace wpmvc;

abstract class App extends \wpmvc\base\App {

    /**
     * @var self
     */
    public static $app;

    public function run() {
        static::$app = $this;

        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
    }

    public function template_redirect() {
        global $wp;

        if ( empty( $this->router->routes ) ) {
            return;
        }

        foreach ( $this->router->routes as $route ) {
            if ( $wp->request === $route['path'] ) {
                $this->controller = new $route['action'][0]();
                $this->action     = $route['action'][1];
            }
        }
    }

}
