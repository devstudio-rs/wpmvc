<?php

namespace wpmvc;

class App extends \wpmvc\base\App {

    /**
     * @var self
     */
    public static $app;

    public function init() {
        static::$app = $this;

        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
    }

    public function template_redirect() {
        global $wp;

        if ( empty( $this->router->routes ) ) {
            return;
        }

        foreach ( $this->router->routes as $route ) {
            if ( $wp->request !== $route['path'] ) {
                continue;
            }

            $controller = $route['action'][0];
            $action     = $route['action'][1];

            if ( ! class_exists( $controller ) ) {
                continue;
            }

            $this->controller = new $controller();

            if ( ! method_exists( $this->controller, $action ) ) {
                $this->controller = null;

                continue;
            }

            $this->action = $action;
        }
    }

}
