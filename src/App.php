<?php

namespace wpmvc;

use wpmvc\models\Model;

class App extends \wpmvc\base\App {

    /**
     * @var string
     */
    public static $version = '0.0.1';

    /**
     * @var self
     */
    public static $app;

    /**
     * @var string
     */
    public static $base_path;

    public function init() {
        if ( static::$app !== null ) {
            return;
        }

        static::$app = $this;

        add_action( 'init',                  array( $this, 'setup' ) );
        add_action( 'template_redirect',     array( $this, 'template_redirect' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public static function setup() {
        $wp_content_dir = str_replace( home_url( '/' ), '', content_url() );
        $dir_paths      = explode( sprintf( '/%s/', $wp_content_dir ), __DIR__ );

        static::$base_path = implode( '/', array(
            content_url(),
            $dir_paths[1],
        ) );
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

            http_response_code( 200 );

            $this->controller->action = $action;
            $this->controller->before_action();

            call_user_func( array( $this->controller, $this->controller->action ) );
        }
    }

    public static function admin_enqueue_scripts() {
        wp_enqueue_style(
            'wpmvc-admin',
            sprintf( '%s/assets/css/wpmvc-admin.css', static::$base_path ),
            array(),
            static::$version
        );
    }

}
