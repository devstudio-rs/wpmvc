<?php

namespace wpmvc\web;

use wpmvc\models\Taxonomy_Model;

class Taxonomy_Controller extends Controller {

    public $model;

    /**
     * Prepare taxonomy controller before adding new taxonomy.
     *
     * @param string $taxonomy
     * @return void
     */
    public function before_add( string $taxonomy ) {
        $model = ( new $this->model() );
        $this->init();

        echo $this->on_action( $model );
        echo $this->on_action_add( $model );
    }

    /**
     * Prepare taxonomy controller before editing taxonomy.
     *
     * @param \WP_Term $taxonomy
     * @return void
     */
    public function before_edit( \WP_Term $taxonomy ) {
        $model = ( new $this->model() )->find_one( $taxonomy->term_id );
        $this->init();

        echo $this->on_action( $model );
        echo $this->on_action_edit( $model );
    }

    /**
     * Initialize taxonomy controller.
     *
     * @return void
     */
    public function init() {
        $this->on_init();
    }

    /**
     * Prepare taxonomy controller before saving taxonomy.
     *
     * @param int $term_id
     * @param int $tt_id
     * @param bool $update
     * @param array $args
     * @return void
     */
    public function before_save( int $term_id, int $tt_id, bool $update, array $args = array() ) {
        if ( isset( $args['fire_after_hooks'] ) && empty( $args['fire_after_hooks'] ) ) {
            return;
        }

        $model = ( new $this->model() )->find_one( $term_id );
        $this->init();

        $this->on_save( $model );
    }

    /**
     * Initialize taxonomy controller.
     *
     * @return void
     */
    public function on_init() {}

    /**
     * Action for saving taxonomy.
     *
     * @param Taxonomy_Model $model
     * @return void
     */
    public function on_save( Taxonomy_Model $model ) {}

    /**
     * General action for taxonomy controller.
     *
     * @param Taxonomy_Model $model
     * @return void
     */
    public function on_action( Taxonomy_Model $model ) {}

    /**
     * Action for adding new taxonomy.
     *
     * @param Taxonomy_Model $model
     * @return void
     */
    public function on_action_add( Taxonomy_Model $model ) {}

    /**
     * Action for editing existing taxonomy.
     *
     * @param Taxonomy_Model $model
     * @return void
     */
    public function on_action_edit( Taxonomy_Model $model ) {}

}
