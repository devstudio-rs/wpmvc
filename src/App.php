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

    public static $version = '1.7.0';

    /**
     * Every application subclass (plugin, theme) MUST redeclare this
     * property (`public static $app;`) to get its own instance slot.
     * Without the redeclaration all subclasses share this one and
     * overwrite each other on init().
     */
    public static $app;

    public static $base_path;
    public static $base_url;

    public function init() {
        $this->ensure_app_property();

        static::$app = $this;
        $this->setup();

        $this->bootstrap();

        add_action( 'template_redirect',     array( $this, 'template_redirect' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    protected function setup() {
        static::$base_path = dirname( dirname( __FILE__ ) );
        static::$base_url  = home_url( str_replace( ABSPATH, '', static::$base_path ) );

        $uploads_dir = wp_get_upload_dir();

        $this->config['aliases'] = wp_parse_args( $this->config['aliases'], array(
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

    /**
     * Warn when an application subclass does not redeclare `public static $app;`,
     * since without it multiple apps share (and overwrite) the same slot.
     *
     * @return void
     */
    private function ensure_app_property() {
        if ( static::class === self::class ) {
            return;
        }

        $property = new \ReflectionProperty( static::class, 'app' );

        if ( $property->getDeclaringClass()->getName() === static::class ) {
            return;
        }

        _doing_it_wrong(
            __METHOD__,
            sprintf(
                'Declare `public static $app;` on %s. Without it, all application classes share the same static slot and overwrite each other. Use %s::app() as a safe alternative.',
                static::class,
                static::class
            ),
            '1.1.0'
        );
    }

    public function template_redirect() {
        global $wp;

        if ( empty( $this->router->routes ) ) {
            return;
        }

        foreach ( $this->router->routes as $route ) {
            $params = $this->router->match_path( $route, $wp->request );

            if ( false === $params ) {
                continue;
            }

            if ( ! $this->router->matches_method( $route, $this->request->method() ) ) {
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

            // Route params are required: when the action declares more
            // required parameters than the route path provides, the route
            // is not available (falls through to a regular WP 404).
            $required = ( new \ReflectionMethod( $this->controller, $action ) )->getNumberOfRequiredParameters();

            if ( count( $params ) < $required ) {
                $this->controller = null;

                continue;
            }

            http_response_code( 200 );

            $this->controller->action = $action;
            $this->controller->before_action();

            call_user_func_array( array( $this->controller, $this->controller->action ), $params );
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
