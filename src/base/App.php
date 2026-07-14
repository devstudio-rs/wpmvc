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
 * @property \wpmvc\components\User $user
 *
 */

class App extends Component {

    protected $config = array();

    public $params = array();
    public $bootstrap = array();

    /**
     * Registered application instances, keyed by concrete class name.
     * Allows the plugin and the theme to each run their own app.
     *
     * @var self[]
     */
    private static $instances = array();

    private $_component_configs = array();
    private $_components = array();

    public function __construct( array $config = array() ) {
        $this->set_config( $config );
        $this->register_components();

        self::$instances[ static::class ] = $this;
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
     * Resolve the application instance for the called class.
     *
     * `Theme::app()` returns the theme instance, `WPMVC::app()` the plugin
     * instance. When called on a class with no registered instance (e.g.
     * `App::app()` from a shared helper), the most recently registered
     * subclass instance is returned.
     *
     * @return self
     * @throws \wpmvc\exceptions\App_Not_Initialized_Exception When no instance exists yet.
     */
    public static function app() : self {
        if ( isset( self::$instances[ static::class ] ) ) {
            return self::$instances[ static::class ];
        }

        $found = null;

        foreach ( self::$instances as $instance ) {
            if ( $instance instanceof static ) {
                $found = $instance;
            }
        }

        if ( $found instanceof self ) {
            return $found;
        }

        throw new \wpmvc\exceptions\App_Not_Initialized_Exception( sprintf(
            'No %1$s application instance has been created yet. Instantiate the application first, e.g. `( new Theme( $config ) )->init()`, before calling %1$s::app().',
            static::class
        ) );
    }

    /**
     * @param array $config
     * @return void
     */
    private function set_config( array $config = array() ) {
        $this->config = array_merge( array(
            'name'       => '',
            'domain'     => 'default',
            'aliases'    => array(),
            'components' => array(),
            'bootstrap'  => array(),
            'options'    => array(),
            'params'     => array(),
        ), $config );

        $this->params    = $this->config['params'];
        $this->bootstrap = $this->config['bootstrap'];
    }

    /**
     * Merge default and user-defined component configs without instantiating.
     *
     * @return void
     */
    private function register_components() {
        $components = $this->get_components();

        if ( ! empty( $this->config['components'] ) ) {
            foreach ( $this->config['components'] as $component_id => $component ) {
                $components[ $component_id ] = ! empty( $components[ $component_id ] ) ?
                    array_merge(
                        $components[ $component_id ],
                        $component
                    ) : $component;
            }
        }

        $this->_component_configs = $components;
    }

    /**
     * Instantiate the components listed in $bootstrap even if they are
     * never accessed, so they can register their hooks eagerly.
     *
     * @return void
     */
    protected function bootstrap() {
        foreach ( $this->bootstrap as $component_id ) {
            $this->__get( $component_id );
        }
    }

    private function create_component( array $config ) {
        $class = $config['class'];
        unset( $config['class'] );

        return new $class( $this->resolve_aliases( $config ) );
    }

    /**
     * Recursively resolve aliases in a component config, so components
     * receive final values and stay decoupled from the app. Class-level
     * defaults never pass through here — they may only use framework
     * aliases (@wpmvc.*, @upload.*, @home, @content), resolved statically
     * at the point of use.
     *
     * @param mixed $value
     * @return mixed
     */
    private function resolve_aliases( $value ) {
        if ( is_array( $value ) ) {
            return array_map( array( $this, 'resolve_aliases' ), $value );
        }

        if ( is_string( $value ) ) {
            return $this->get_alias( $value );
        }

        return $value;
    }

    /**
     * Resolve aliases against the called class's application instance.
     *
     * @param string $value
     * @return string
     */
    public static function alias( $value ) {
        return static::app()->get_alias( $value );
    }

    /**
     * Resolve aliases against this application instance.
     *
     * @param string $value
     * @return string
     */
    public function get_alias( $value ) {
        if ( empty( $this->config['aliases'] ) ) {
            return $value;
        }

        return strtr( $value, $this->config['aliases'] );
    }

    /**
     * @param string $text
     * @param string|null $domain
     * @return string
     */
    public static function t( string $text, $domain = null ) : string {
        if ( empty( $domain ) ) {
            $domain = static::app()->config['domain'];
        }

        return translate( $text, $domain );
    }

    /**
     * @return array
     */
    public function get_config() : array {
        return $this->config;
    }

    private function get_components() : array {
        return array(
            'router' => array(
                'class' => \wpmvc\web\Router::class,
            ),
            'request' => array(
                'class' => \wpmvc\web\Request::class,
            ),
            'user' => array(
                'class' => \wpmvc\components\User::class,
            ),
        );
    }

}
