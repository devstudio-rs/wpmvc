<?php

namespace wpmvc;

class App {

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
