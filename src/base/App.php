<?php

namespace wpmvc\base;

abstract class App extends Component {

    /**
     * @var self
     */
    public static $instance;

    public function __construct( array $config = array() ) {}

    public static function init( array $config = array() ) {
        if ( static::$instance === null ) {
            static::$instance = new static( $config );
        }
    }

}
