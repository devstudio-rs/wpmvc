<?php

namespace wpmvc\models;

use wpmvc\web\Meta_Box_Controller;

abstract class Post_Model extends Active_Model {

    const STATUS_ANY     = 'any';
    const STATUS_PUBLISH = 'publish';
    const STATUS_DRAFT   = 'draft';
    const STATUS_PRIVATE = 'private';
    const STATUS_TRASH   = 'trash';

    public $ID;
    public $post_type    = 'post';
    public $post_status  = self::STATUS_DRAFT;

    /**
     * Trigger initialization after custom post type is registered.
     *
     * @return void
     */
    protected function init() {}

    /**
     * Return $args values for register_post_type.
     *
     * @return array
     */
    protected function registry() : array {
        return array();
    }

    /**
     * @return array
     */
    protected function registry_labels() : array {
        return array();
    }

    /**
     * @return string[]
     */
    protected function registry_supports() : array {
        return array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
        );
    }

    /**
     * @return array
     */
    protected function registry_rewrite() : array {
        return array();
    }

    /**
     * @param array $params
     * @return \WP_Query
     */
    public function query( array $params = array() ) : \WP_Query {
        $params = array_merge( array(
            'post_type'         => $this->post_type,
            'posts_per_page'    => -1,
            'post_status'       => array(
                static::STATUS_DRAFT,
                static::STATUS_PRIVATE,
                static::STATUS_PUBLISH
            ),
        ), array_merge(
            $this->query_params,
            $params
        ) );

        return new \WP_Query( $params );
    }

    /**
     * @return $this|mixed|null
     */
    public function one() {
        $query = $this->query( array_merge( $this->query_params, array(
            'posts_per_page' => 1,
        ) ) );

        if ( ! $query->have_posts() ) {
            return null;
        }

        $post = current( $query->posts );

        $this->set_attributes( (array) $post );

        $meta_attributes = array();
        $meta_keys       = $this->get_attributes_meta_keys();

        foreach ( $meta_keys as $meta_key ) {
            $value = get_post_meta( $post->ID, $meta_key );
            $meta_attributes[ $meta_key ] = $value[0] ?? $this->{ $meta_key };
        }

        $this->set_attributes( $meta_attributes );

        return $this;
    }

    /**
     * @return array
     */
    public function all() : array {
        $query = $this->query( array_merge( $this->query_params, array(
            'fields' => 'ids',
        ) ) );

        if ( ! $query->have_posts() ) {
            return array();
        }

        $items = array();

        foreach ( $query->posts as $post_id ) {
            $items[] = static::find_one( $post_id );
        }

        return $items;
    }

    /**
     * @return $this
     */
    public function published() : self {
        $this->query_params = array_merge( $this->query_params, array(
            'post_status' => static::STATUS_PUBLISH,
        ) );

        return $this;
    }

    /**
     * @return array
     */
    public function get_attributes_meta_keys() : array {
        return array_diff(
            array_keys( get_class_vars( static::class ) ),
            array_keys( get_class_vars( self::class ) )
        );
    }

    /**
     * Triggered before saving validation starts.
     *
     * @return void
     */
    public function before_save() {}

    /**
     * Triggered after model is saved and provided with ID.
     *
     * @return void
     */
    public function after_save() {}

    /**
     * @return bool
     */
    public function save( $validate = true ) : bool {
        $this->before_save();

        if ( $validate && ! $this->validate() ) {
            return false;
        }

        $attributes = $this->get_attributes();
        $meta_keys  = $this->get_attributes_meta_keys();

        $post_id = empty( $this->ID ) ?
            wp_insert_post( $attributes, true, false ) :
            wp_update_post( $attributes, true, false );

        if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
            return false;
        }

        $this->ID = $post_id;

        foreach ( $meta_keys as $meta_key ) {
            update_post_meta( $this->ID, $meta_key, $attributes[ $meta_key ] );
        }

        $this->after_save();

        return true;
    }

    /**
     * @return array|false|\WP_Post|null
     */
    public function delete() {
        return wp_delete_post( $this->ID );
    }

    public static function register() {
        $model    = new static();
        $registry = $model->registry();

        if ( empty( $registry ) ) {
            return false;
        }

        $post_type = $model->post_type;
        $args      = array_merge( array(
            'labels'    => $model->registry_labels(),
            'supports'  => $model->registry_supports(),
            'rewrite'   => $model->registry_rewrite(),
        ), $registry );

        register_post_type( $post_type, $args );

        $model->init();
    }

    /**
     * @param string $controller
     * @param string $title
     * @param array $args
     * @return void
     */
    public function add_meta_box( string $controller, string $title, array $args = array() ) {
        /** @var Meta_Box_Controller $controller */
        $controller = new $controller();

        $args = array_merge( array(
            'title'         => $title,
            'model'         => static::class,
            'screen'        => null,
            'context'       => 'advanced',
            'priority'      => 'default',
            'callback_args' => null,
        ), $args );

        $controller->set_attributes( $args );

        add_action( sprintf( 'add_meta_boxes_%s', $this->post_type ), array( $controller, 'init' ) );
        add_action( 'wp_after_insert_post', array( $controller, 'before_save' ), 10, 2 );
    }

}
