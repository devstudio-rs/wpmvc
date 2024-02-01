<?php

namespace wpmvc\interfaces;

interface Active_Model_Interface {

    /**
     * @param array $params
     * @return \WP_Query
     */
    public function query( array $params = array() );

    /**
     * @return mixed
     */
    public function one();

    /**
     * @return mixed
     */
    public function all();

    /**
     * @return bool
     */
    public function save() : bool;

    /**
     * @param array $params
     * @return mixed
     */
    public static function find( array $params = array() );

    /**
     * @param int $id
     * @return mixed
     */
    public static function find_one( int $id );

}
