<?php

namespace wpmvc\models;

use wpmvc\base\Model;
use wpmvc\interfaces\Active_Model_Interface;

abstract class Active_Model extends Model implements Active_Model_Interface {

    /**
     * @var array
     */
    protected $query_params = array();

    /**
     * @param array $params
     * @return static
     */
    public static function find( array $params = array() ) : self {
        $model = new static();

        $model->query_params = $params;

        return $model;
    }

    /**
     * @param int $id
     * @return null|Active_Model
     */
    public static function find_one( int $id ) {
        return static::find( array(
            'p' => $id,
        ) )->one();
    }

    /**
     * @param array $params
     * @return array
     */
    public static function find_all( array $params = array() ) : array {
        return static::find( $params )
            ->all();
    }

    /**
     * @param string|Taxonomy_Model $taxonomy
     * @param array $args
     * @return self
     */
    public function where_taxonomy( $taxonomy, array $args = array() ) : self {
        if ( class_exists( $taxonomy ) ) {
            $taxonomy = (new $taxonomy())->taxonomy;
        }

        if ( empty( $this->query_params['tax_query'] ) ) {
            $this->query_params['tax_query'] = array();
        }

        $this->query_params['tax_query'][] = array(
            'taxonomy' => $taxonomy,
            'field'    => current( array_keys( $args ) ),
            'terms'    => current( array_values( $args ) ),
            'operator' => 'IN',
            'include_children' => true,
        );

        return $this;
    }

}
