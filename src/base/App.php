<?php

namespace wpmvc\base;

/**
 * Class App
 *
 * @since 1.0.0
 * @package wpmvc\base
 *
 * @property array $config
 * @property array $params
 * @property \wpmvc\web\Request $request
 * @property \wpmvc\web\Router $router
 * @property \wpmvc\web\View $view
 * @property \wpmvc\web\Controller $controller
 * @property \wpmvc\web\Options $options
 * @property \wpmvc\components\Logger $logger
 *
 */

abstract class App extends Component {

    protected static $config = array();

    public $params = array();
    public $request;
    public $router;
    public $view;
    public $controller;
    public $options;
    public $logger;

    public function __construct( array $config = array() ) {
        $this->set_config( $config );

        $this->setup_components();
    }

    /**
     * @param array $config
     * @return void
     */
    private function set_config( array $config = array() ) {
        static::$config = array_merge( array(
            'name'       => '',
            'domain'     => 'default',
            'aliases'    => array(),
            'components' => array(),
            'options'    => array(),
            'params'     => array(),
        ), $config );

        $this->params = static::$config['params'];
    }

    /**
     * Setup application components.
     *
     * @return void
     */
    private function setup_components() {
        $components = $this->get_components();

        if ( ! empty( static::$config['components'] ) ) {
            foreach ( static::$config['components'] as $component_id => $component ) {
                $components[ $component_id ] = ! empty( $components[ $component_id ] ) ?
                    array_merge(
                        $components[ $component_id ],
                        $component
                    ) : $component;
            }
        }

        foreach ( $components as $component_id => $component ) {
            $component_class = $component['class'];

            unset( $component['class'] );

            $this->{ $component_id } = new $component_class( $component );
        }
    }

    public static function alias( $value ) {
        if ( empty( static::$config['aliases'] ) ) {
            return $value;
        }

        return strtr( $value, static::$config['aliases'] );
    }

    /**
     * @param string $text
     * @param string|null $domain
     * @return string
     */
    public static function t( string $text, $domain = null ) : string {
        if ( empty( $domain ) ) {
            $domain = static::$config['domain'];
        }

        return translate( $text, $domain );
    }

    /**
     * @return array
     */
    public function get_config() : array {
        return static::$config;
    }

    private function get_components() : array {
        return array(
            'router' => array(
                'class' => \wpmvc\web\Router::class,
            ),
            'request' => array(
                'class' => \wpmvc\web\Request::class,
            ),
            'view' => array(
                'class'  => \wpmvc\web\View::class,
                'assets' => array(),
            ),
            'options' => array(
                'class'   => \wpmvc\web\Options::class,
                'label'   => 'Options',
                'config'  => array(),
            ),
            'logger' => array(
                'class' => \wpmvc\components\Logger::class,
            ),
        );
    }

}
