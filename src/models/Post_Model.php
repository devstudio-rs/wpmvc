<?php

namespace wpmvc\models;

use wpmvc\base\Active_Model;

class Post_Model extends Active_Model {

    const STATUS_ANY     = 'any';
    const STATUS_PUBLISH = 'publish';
    const STATUS_DRAFT   = 'draft';
    const STATUS_TRASH   = 'trash';

    public $ID;
    public $post_type = 'post';

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
            wp_insert_post( $attributes, true ) :
            wp_update_post( $attributes, true );

        if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
            return false;
        }

        $this->ID = $post_id;

        foreach ( $meta_keys as $meta_key ) {
            update_post_meta( $this->ID, $meta_key, $attributes[ $meta_key ] );
        }

        return true;
    }

}
