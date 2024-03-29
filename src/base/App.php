<?php

namespace wpmvc\base;

abstract class App extends Component {

    /**
     * @var array
     */
    protected static $config = array();

    /** @var array  */
    public $params = array();

    /** @var \wpmvc\web\Request */
    public $request;

    /** @var \wpmvc\web\Router */
    public $router;

    /** @var \wpmvc\web\View */
    public $view;

    /** @var \wpmvc\web\Controller */
    public $controller;

    /** @var \wpmvc\web\Options */
    public $options;

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
            $this->{ $component_id } = new $component['class']( $component );
        }
    }

    public static function alias( $value ) {
        if ( empty( static::$config['aliases'] ) ) {
            return $value;
        }

        $parts = explode( '/', $value );
        $alias = $parts[0];

        if ( empty( static::$config['aliases'][ $alias ] ) ) {
            return $value;
        }

        return str_replace( $alias, static::$config['aliases'][ $alias ], $value );
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
                'label'   => __( 'Options', 'wpmvc' ),
                'options' => array(),
            ),
        );
    }

}
