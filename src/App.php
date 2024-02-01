<?php

namespace wpmvc;

class App extends \wpmvc\base\App {

    /**
     * @var self
     */
    public static $app;

    public static function init( array $config = array() ) {
        if ( static::$app === null ) {
            static::$app = new static( $config );
        }
    }

}
