<?php

namespace wpmvc\base;

abstract class Request extends Component {

    const REQUEST_GET   = 'get';
    const REQUEST_POST  = 'post';

    /**
     * @param string $method
     * @param string $attribute
     * @param mixed $default
     * @param bool $safe
     * @return array|mixed|null
     */
    public function request( string $method = 'get', string $attribute = null, $default = null ) {
        $params = strtolower( $method ) === static::REQUEST_GET ?
            $_GET : $_POST;

        if ( ! isset( $attribute ) ) {
            return $params;
        }

        if ( isset( $attribute ) && ! isset( $params[ $attribute ] ) ) {
            return $default;
        }

        return $params[ $attribute ];
    }

    /**
     * @param string|null $attribute
     * @param $default
     * @param bool $safe
     * @return array|mixed|null
     */
    public function get( string $attribute = null, $default = null ) {
        return $this->request( static::REQUEST_GET, $attribute, $default );
    }

    /**
     * @param string|null $attribute
     * @param mixed $default
     * @param bool $safe
     * @return array|mixed|null
     */
    public function post( string $attribute = null, $default = null, bool $safe = true ) {
        return $this->request( static::REQUEST_POST, $attribute, $default );
    }

}
