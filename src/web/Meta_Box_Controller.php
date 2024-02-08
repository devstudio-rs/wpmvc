<?php

namespace wpmvc\web;

use wpmvc\models\Post_Model;

class Meta_Box_Controller extends \wpmvc\base\Controller {

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $context  = 'advanced';

    /**
     * @var string
     */
    public $priority = 'default';

    /**
     * @var array
     */
    public $callback_args;

    /**
     * @var string|\WP_Post
     */
    public $model;

    /**
     * @return void
     */
    public function init() {
        $model     = $this->model;
        $post_type = ( new $model() )->post_type;
        $this->id  = $post_type;

        $this->before_action();

        add_meta_box(
            $this->id,
            $this->title,
            function( $post ) use ( $model ) {
                $new_model = $model::find_one( $post->ID );

                if ( empty( $new_model ) ) {
                    $new_model = new $model();
                }

                $this->set_model( $new_model );
                $this->on_action( $new_model );
            },
            $post_type,
            $this->context,
            $this->priority,
            $this->callback_args
        );
    }

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
    public function before_save( $post_id, $post ) {
        $model = $this->model::find_one( $post_id );

        if ( empty( $model ) ) {
            $model = new $this->model();
        }

        if ( $model->post_type !== $post->post_type ) {
            return;
        }

        $this->on_save( $model );
    }

    /**
     * @param Post_Model $model
     * @return void
     */
    public function on_save( Post_Model $model ) {}

    /**
     * @param string|Post_Model $model
     * @return void
     */
    public function set_model( $model ) {
        $this->model = $model;
    }

}
