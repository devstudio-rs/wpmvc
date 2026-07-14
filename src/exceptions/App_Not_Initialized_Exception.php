<?php

namespace wpmvc\exceptions;

/**
 * Thrown when App::app() is called before any application instance exists.
 * This always indicates a framework usage bug, never a legitimate state.
 *
 * @since 1.1.0
 * @package wpmvc\exceptions
 */
class App_Not_Initialized_Exception extends \RuntimeException {}
