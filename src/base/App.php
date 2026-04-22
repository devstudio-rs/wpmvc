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

class App extends Component {

    protected static $config = array();

    public $params = array();

    private $_component_configs = array();
    private $_components = array();

    public function __construct( array $config = array() ) {
        $this->set_config( $config );
        $this->register_components();
    }

    public function __get( $name ) {
        if ( array_key_exists( $name, $this->_components ) ) {
            return $this->_components[ $name ];
        }

        if ( isset( $this->_component_configs[ $name ] ) ) {
            $this->_components[ $name ] = $this->create_component( $this->_component_configs[ $name ] );
            return $this->_components[ $name ];
        }

        return null;
    }

    public function __set( $name, $value ) {
        $this->_components[ $name ] = $value;
    }

    public function __isset( $name ) {
        return isset( $this->_components[ $name ] ) || isset( $this->_component_configs[ $name ] );
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
     * Merge default and user-defined component configs without instantiating.
     *
     * @return void
     */
    private function register_components() {
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

        $this->_component_configs = $components;
    }

    private function create_component( array $config ) {
        $class = $config['class'];
        unset( $config['class'] );
        return new $class( $config );
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
        );
    }

}
