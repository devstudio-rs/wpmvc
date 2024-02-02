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
    public function request( string $method = 'get', string $attribute = null, $default = null, bool $safe = true ) {
        $params = strtolower( $method ) === static::REQUEST_GET ?
            $_GET : $_POST;

        if ( $safe ) {
            $params = array_map( 'sanitize_text_field', $params );
        }

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
    public function get( string $attribute = null, $default = null, bool $safe = true ) {
        return $this->request( static::REQUEST_GET, $attribute, $default, $safe );
    }

    /**
     * @param string|null $attribute
     * @param mixed $default
     * @param bool $safe
     * @return array|mixed|null
     */
    public function post( string $attribute = null, $default = null, bool $safe = true ) {
        return $this->request( static::REQUEST_POST, $attribute, $default, $safe );
    }

}
