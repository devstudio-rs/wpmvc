<?php

namespace wpmvc\web;

use wpmvc\base\App;
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
     * Post model class name.
     *
     * @var string
     */
    public $model;

    /**
     * Register the meta box. Runs on `add_meta_boxes_{post_type}`.
     *
     * @return void
     */
    public function init() {
        $post_type = ( new $this->model() )->post_type;

        if ( empty( $this->title ) ) {
            $this->title = $this->get_default_title();
        }

        $this->on_init();
        $this->before_action();

        add_action( 'admin_notices', array( $this, 'render_notices' ) );

        add_meta_box(
            $this->get_id(),
            $this->title,
            function( $post ) {
                wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_action() );

                $this->on_action( ( new $this->model() )->populate( $post ) );
            },
            $post_type,
            $this->context,
            $this->priority,
            $this->callback_args
        );
    }

    /**
     * Deterministic meta box ID derived from the controller class name.
     * WordPress persists per-user screen preferences (open/closed, hidden)
     * by this ID, so it must be stable between requests.
     *
     * @return string
     */
    public function get_id() : string {
        if ( empty( $this->id ) ) {
            $this->id = strtolower( str_replace( '_', '-', $this->get_class_name() ) );
        }

        return $this->id;
    }

    public function on_init() {}

    /**
     * Render the meta box.
     *
     * @param Post_Model $model
     * @return void
     */
    public function on_action( Post_Model $model ) {}

    /**
     * Save handler. Runs on `wp_after_insert_post` — that hook is used
     * (instead of `save_post_{post_type}`) because Post_Model::save()
     * suppresses it, which keeps model saves inside on_save() from
     * re-triggering this handler recursively.
     *
     * @param integer $post_id
     * @param \WP_Post $post
     * @return void
     */
    public function save( int $post_id, \WP_Post $post ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $nonce_action = $this->get_nonce_action();

        if ( ! isset( $_POST[ $nonce_action ] ) ||
            ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce_action ] ) ), $nonce_action ) ) {
            return;
        }

        $model = new $this->model();

        if ( $model->post_type !== $post->post_type ) {
            return;
        }

        $this->on_save( $model->populate( $post ) );
    }

    /**
     * Default save behaviour: load request data into the model, validate
     * and save. Validation errors are stashed and shown as an admin notice
     * after the post-save redirect. Override for custom behaviour.
     *
     * @param Post_Model $model
     * @return void
     */
    public function on_save( Post_Model $model ) {
        if ( ! $model->load( App::app()->request->post() ) ) {
            return;
        }

        if ( $model->save() ) {
            return;
        }

        $messages = array();

        foreach ( $model->get_errors() as $error ) {
            foreach ( $error['messages'] as $message ) {
                $messages[] = sprintf( '%s: %s', $model->get_attribute_label( $error['attribute'] ), $message );
            }
        }

        if ( empty( $messages ) ) {
            $messages[] = 'Saving failed.';
        }

        set_transient( $this->get_notices_key(), $messages, 60 );
    }

    /**
     * Print stashed validation errors on the edit screen after redirect.
     *
     * @return void
     */
    public function render_notices() {
        $messages = get_transient( $this->get_notices_key() );

        if ( empty( $messages ) ) {
            return;
        }

        delete_transient( $this->get_notices_key() );

        foreach ( (array) $messages as $message ) {
            printf(
                '<div class="notice notice-error"><p><strong>%s:</strong> %s</p></div>',
                esc_html( $this->title ),
                esc_html( $message )
            );
        }
    }

    private function get_nonce_action() : string {
        return sprintf( 'wpmvc_meta_box_%s', $this->get_id() );
    }

    private function get_notices_key() : string {
        return sprintf( 'wpmvc_meta_box_%s_%d', $this->get_id(), get_current_user_id() );
    }

    private function get_default_title() : string {
        $title = preg_replace( '/_?controller$/i', '', $this->get_class_name() );

        return ucwords( strtolower( str_replace( '_', ' ', $title ) ) );
    }

}
