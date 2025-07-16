<?php

namespace wpmvc;

/**
 * Class App
 *
 * @since 1.0.0
 * @package wpmvc\base
 *
 * @property string $version
 * @property self $app
 * @property string $base_path
 */

class App extends \wpmvc\base\App {

    public static $version = '1.0.20';
    public static $app;

    public static $base_path;
    public static $base_url;

    public function init() {
        if ( static::$app !== null ) {
            return;
        }

        static::$app = $this;
        static::setup();

        add_action( 'template_redirect',     array( $this, 'template_redirect' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public static function setup() {
        static::$base_path = dirname( dirname( __FILE__ ) );
        static::$base_url  = home_url( str_replace( ABSPATH, '', static::$base_path ) );

        $uploads_dir = wp_get_upload_dir();

        static::$config['aliases'] = wp_parse_args( static::$config['aliases'], array(
            '@wpmvc.path'     => static::$base_path,
            '@wpmvc.url'      => static::$base_url,
            '@home'           => home_url(),
            '@content'        => content_url(),
            '@upload.basedir' => $uploads_dir['basedir'],
            '@upload.baseurl' => $uploads_dir['baseurl'],
            '@upload.dir'     => $uploads_dir['path'],
            '@upload.url'     => $uploads_dir['baseurl'],
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
            static::alias( '@wpmvc.url/assets/css/wpmvc-admin.css' ),
            array(),
            static::$version
        );
    }

}
