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
        add_action( 'admin_init',            array( $this, 'setup_options' ) );
        add_action( 'admin_menu',            array( $this, 'setup_options_pages' ) );
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

            $this->controller->action = $action;
            $this->controller->before_action();

            call_user_func( array( $this->controller, $this->controller->action ) );
        }
    }

    public function setup_options() {
        if ( empty( static::$config['options'] ) ) {
            return;
        }

        foreach ( static::$config['options'] as $option ) {
            foreach ( $option['items'] as $item ) {
                register_setting( $option['id'], $item['name'] );
            }
        }
    }

    public function setup_options_pages() {
        if ( empty( static::$config['options'] ) ) {
            return;
        }

        add_menu_page(
            __( 'Theme Options', 'wpmvc' ),
            __( 'Theme Options', 'wpmvc' ),
            'manage_options',
            'theme-options'
        );

        foreach ( static::$config['options'] as $index => $option ) {
            add_submenu_page(
                'theme-options',
                $option['label'],
                $option['label'],
                'manage_options',
                ( $index === 0 ? 'theme-options' : $option['id'] ),
                array( $this, 'setup_options_template' )
            );
        }
    }

    /**
     * Setup theme options template.
     *
     * @return void
     */
    public function setup_options_template() {
        $page_slug    = static::$app->request->get( 'page' );
        $option_index = array_search( $page_slug, array_column( static::$config['options'], 'id' ) );
        $option_index = ! empty( $option_index ) ? $option_index : 0;

        $options = apply_filters( 'wpmvc_options', static::$config['options'] );
        $options = $options[ $option_index ];

        $model = new Model();

        foreach ( $options['items'] as &$item ) {
            $item = apply_filters( 'wpmvc_options_' . $item['name'], $item );

            $default_value = ! empty( $item['default'] ) ? $item['default'] : null;

            $model->{ $item['name'] } = get_option( $item['name'], $default_value );

            register_setting( $options['id'], $item['name'] );
        }

        unset( $item );

        load_template( sprintf( '%s/views/theme-options.php', __DIR__ ), true, array(
            'model'   => $model,
            'options' => $options,
        ) );
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
