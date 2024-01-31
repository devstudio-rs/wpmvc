<?php

namespace wpmvc\base;

abstract class Active_Model extends Model {

    private $query_params = array();

    public static function find( $params = array() ) : self {
        $model = new static();

        $model->set_attribute( 'query_params', $params );

        return $model;
    }

    public function one() {}

    public function all() {}

}
