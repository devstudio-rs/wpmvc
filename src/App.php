<?php

namespace wpmvc;

abstract class App extends \wpmvc\base\App {

    /**
     * @var self
     */
    public static $app;

    public function run() {
        static::$app = $this;
    }

}
