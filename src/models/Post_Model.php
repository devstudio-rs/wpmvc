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

    public function init() {}

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
        ), $params );

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

    public function all() {}

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
     * @return bool
     */
    public function save() : bool {
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

        return true;
    }

    /**
     * @return array|false|\WP_Post|null
     */
    public function delete() {
        return wp_delete_post( $this->ID );
    }

    /**
     * @param array $args
     * @return \WP_Error|\WP_Post_Type
     */
    public static function register( array $args = array() ) {
        $model     = new static();
        $post_type = $model->post_type;
        $args      = array_merge( array(
            'labels'             => array(
                'name' => ucfirst( strtolower( $post_type ) ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array(
                'slug'       => $post_type,
                'with_front' => false,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 10,
            'supports'           => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
            ),
        ), $args );

        $model->init();

        return register_post_type( $post_type, $args );
    }

    public function add_meta_box( string $meta_box_model_class ) {
        /** @var Meta_Box_Model $meta_box_model */
        $meta_box_model = new $meta_box_model_class();

        $meta_box_model->set_model( static::class );

        /** @var Meta_Box_Controller $controller */
        $controller = new $meta_box_model->controller();

        $controller->model = static::class;
        $controller->meta_box_model = $meta_box_model_class;

        add_action( sprintf( 'add_meta_boxes_%s', $this->post_type ), array( $controller, 'init' ) );
        add_action( 'wp_after_insert_post', array( $controller, 'before_save' ), 10, 2 );
    }

}
