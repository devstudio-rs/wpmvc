<?php

namespace wpmvc\models;

use wpmvc\web\Taxonomy_Controller;

abstract class Taxonomy_Model extends Active_Model {

    public $taxonomy = 'category';

    protected function init() {}

    public function registry() : array {
        return array();
    }

    public function registry_labels() : array {
        return array();
    }

    public function registry_object_type() : array {
        return array();
    }

    public static function register() {
        $model    = new static();
        $registry = $model->registry();

        if ( empty( $registry ) ) {
            return false;
        }

        $args = array_merge( array(
            'labels' => $model->registry_labels(),
            'sort'   => true,
        ), $registry );

        register_taxonomy( $model->taxonomy, $model->registry_object_type(), $args );

        $model->init();
    }

    /**
     * @return int
     */
    public function get_id() {
        return $this->term_id;
    }

    /**
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * @param array $params
     * @return \WP_Term_Query
     */
    public function query( array $params = array() ) : \WP_Term_Query {
        $params = array_merge( array(
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ), array_merge(
            $this->query_params,
            $params
        ) );

        return new \WP_Term_Query( $params );
    }

    /**
     * @return $this|mixed|null
     */
    public function one() {
        $query = $this->query( array_merge( $this->query_params, array(
            'number' => 1,
        ) ) );

        $terms = $query->get_terms();

        if ( empty( $terms ) ) {
            return null;
        }

        $term = current( $terms );

        $this->set_attributes( (array) $term );

        $meta_attributes = array();
        $meta_keys       = $this->get_attributes_meta_keys();

        foreach ( $meta_keys as $meta_key ) {
            $value = get_term_meta( $term->term_id, $meta_key );
            $meta_attributes[ $meta_key ] = $value[0] ?? $this->{ $meta_key };
        }

        $this->set_attributes( $meta_attributes );

        return $this;
    }

    public function all() : array {
        $query = $this->query( array_merge( $this->query_params, array(
            'fields' => 'ids',
        ) ) );

        $terms = $query->get_terms();

        if ( empty( $terms ) ) {
            return array();
        }

        $items = array();

        foreach ( $terms as $term_id ) {
            $items[] = static::find_one( $term_id );
        }

        return $items;
    }

    public static function find_one( int $id ) {
        return static::find( array(
            'include' => array( $id ),
        ) )->one();
    }

    public function save() : bool {
        $attributes = $this->get_attributes();
        $meta_keys  = $this->get_attributes_meta_keys();

        $attributes['fire_after_hooks'] = false;

        $term = empty( $this->term_id ) ?
            wp_insert_term( $this->name, $this->taxonomy, $attributes ) :
            wp_update_term( $this->term_id, $this->taxonomy, $attributes );

        if ( empty( $term ) || is_wp_error( $term ) ) {
            return false;
        }

        $this->term_id = $term['term_id'];

        if ( empty( $meta_keys ) ) {
            return true;
        }

        foreach ( $meta_keys as $meta_key ) {
            update_term_meta( $this->term_id, $meta_key, $attributes[ $meta_key ] );
        }

        return true;
    }

    /**
     * Delete term.
     *
     * @return bool
     */
    public function delete() : bool {
        return wp_delete_term( $this->term_id, $this->taxonomy );
    }

    public function get_attributes_meta_keys() : array {
        return array_diff(
            array_keys( get_class_vars( static::class ) ),
            array_keys( get_class_vars( self::class ) )
        );
    }

    public function add_meta_controller( string $controller, array $args = array() ) {
        /** @var Taxonomy_Controller $controller */
        $controller = new $controller();

        $args = array_merge( array(
            'model' => static::class,
        ), $args );

        $controller->set_attributes( $args );

        add_action( sprintf( '%s_add_form_fields', $this->taxonomy ), array( $controller, 'before_add' ) );
        add_action( sprintf( '%s_edit_form', $this->taxonomy ), array( $controller, 'before_edit' ) );
        add_action( sprintf( 'saved_%s', $this->taxonomy ), array( $controller, 'before_save' ), 10, 4 );
    }
}
