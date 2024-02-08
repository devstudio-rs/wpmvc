<?php

namespace wpmvc\web;

use wpmvc\models\Meta_Box_Model;
use wpmvc\models\Post_Model;

class Meta_Box_Controller extends \wpmvc\base\Controller {

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $meta_box_model;

    /**
     * @return void
     */
    public function init() {
        /** @var Meta_Box_Model $meta_box_model */
        $meta_box_model = new $this->meta_box_model();

        $meta_box_model->init();

        $post_type = ( new $this->model() )->post_type;

        $this->on_init();
        $this->before_action();

        add_meta_box(
            $meta_box_model->id,
            $meta_box_model->title,
            function( $post ) {
                $model = $this->model::find_one( $post->ID );

                if ( empty( $model ) ) {
                    $model = new $this->model();
                }

                $this->on_action( $model );
            },
            $post_type,
            $meta_box_model->context,
            $meta_box_model->priority,
            $meta_box_model->callback_args
        );
    }

    public function on_init() {}

    /**
     * @param Post_Model $model
     * @return void
     */
    public function on_action( Post_Model $model ) {}

    /**
     * @param integer $post_id
     * @param \WP_Post $post
     * @return void
     */
    public function before_save( int $post_id, \WP_Post $post ) {
        if ( ( new $this->model() )->post_type !== $post->post_type ) {
            return;
        }

        $model = $this->model::find_one( $post_id );

        if ( empty( $model ) ) {
            $model = new $this->model();
        }

        $this->on_save( $model );
    }

    /**
     * @param Post_Model $model
     * @return void
     */
    public function on_save( Post_Model $model ) {}

}
