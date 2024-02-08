<?php

namespace wpmvc\models;

use wpmvc\base\Model;

abstract class Meta_Box_Model extends Model {

    public $id;
    public $title;
    public $context       = 'advanced';
    public $priority      = 'default';
    public $callback_args = array();

    public $model;
    public $controller;

    public function init() {}

    /**
     * @param string $id
     * @return void
     */
    public function set_id( string $id ) {
        $this->id = $id;
    }

    /**
     * @param string $title
     * @return void
     */
    public function set_title( string $title ) {
        $this->title = $title;
    }

    /**
     * @param string $context
     * @return void
     */
    public function set_context( string $context ) {
        $this->context = $context;
    }

    /**
     * @param string $priority
     * @return void
     */
    public function set_priority( string $priority ) {
        $this->priority = $priority;
    }

    /**
     * @param array $args
     * @return void
     */
    public function set_callback_args( array $args = array() ) {
        $this->callback_args = $args;
    }

    /**
     * Set post model.
     *
     * @param string $model
     * @return void
     */
    public function set_model( string $model ) {
        $this->model = $model;
    }

    /**
     * Set meta box controller.
     *
     * @param string $controller
     * @return void
     */
    public function set_controller( string $controller ) {
        $this->controller = $controller;
    }

}
