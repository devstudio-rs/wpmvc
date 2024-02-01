<?php

namespace wpmvc\base;

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
    public static function find( array $params = array() ) {
        $model = new static();

        $model->query_params = $params;

        return $model;
    }

    /**
     * @param int $id
     * @return static
     */
    public static function find_one( int $id ) : self {
        return static::find( array(
            'p' => $id,
        ) )->one();
    }

}
