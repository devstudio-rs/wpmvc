<?php

namespace wpmvc\base;

abstract class App extends Component {

    /**
     * @var array
     */
    protected static $config = array();

    /**
     * @var Request
     */
    public $request;

    /** @var \wpmvc\web\Router */
    public $router;

    /**
     * @var Controller
     */
    public $controller;

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
            'aliases'    => array(),
            'components' => array(),
        ), $config );
    }

    /**
     * Setup application components.
     *
     * @return void
     */
    private function setup_components() {
        $this->router = new \wpmvc\web\Router();

        if ( empty( static::$config['components'] ) ) {
            return;
        }

        foreach ( static::$config['components'] as $component => $params ) {
            $this->{ $component } = new $params['class']( $params );
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

}
