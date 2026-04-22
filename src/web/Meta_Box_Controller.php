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
     * @var string|array|\WP_Screen
     */
    public $screen;

    /**
     * @var string
     */
    public $context = 'advanced';

    /**
     * @var string
     */
    public $priority = 'default';

    /**
     * @var array
     */
    public $callback_args;

    /**
     * @var string
     */
    public $model;

    /**
     * @return void
     */
    public function init() {
        $post_type = ( new $this->model() )->post_type;

        $this->id = sprintf( '%s-%d', $post_type, rand( 100000, 999999 ) );

        $this->on_init();
        $this->before_action();

        add_meta_box(
            $this->id,
            $this->title,
            function( $post ) use ( $post_type ) {
                $model = $this->model::find_one( $post->ID );

                if ( empty( $model ) ) {
                    $model = new $this->model();
                }

                wp_nonce_field(
                    sprintf( 'wpmvc_meta_box_%s', $post_type ),
                    sprintf( 'wpmvc_meta_box_%s', $post_type )
                );

                $this->on_action( $model );
            },
            $post_type,
            $this->context,
            $this->priority,
            $this->callback_args
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
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $nonce_action = sprintf( 'wpmvc_meta_box_%s', $post->post_type );

        if ( ! isset( $_POST[ $nonce_action ] ) || ! wp_verify_nonce( $_POST[ $nonce_action ], $nonce_action ) ) {
            return;
        }

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
