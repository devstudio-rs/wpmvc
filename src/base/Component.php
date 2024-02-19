<?php

namespace wpmvc\base;

abstract class Component {

    /**
     * @param array $attributes
     * @return bool
     */
    public function set_attributes( array $attributes = array() ) : bool {
        if ( empty( $attributes ) ) {
            return false;
        }

        foreach ( $attributes as $name => $value ) {
            $this->{ $name } = $value;
        }

        return true;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set_attribute( string $name, $value ) : bool {
        return $this->set_attributes( array(
            $name => $value,
        ) );
    }

    /**
     * @return array
     */
    public function get_attributes() : array {
        return get_object_vars( $this );
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get_attribute( string $name ) {
        return $this->{ $name };
    }

    /**
     * @return string
     */
    public function get_class_name() : string {
        $class_name = explode( '\\', get_class( $this ) );

        return end( $class_name );
    }

    /**
     * Define array of labels for defines attributes.
     *
     * @return array
     */
    public function attribute_labels() : array {
        return array();
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function get_attribute_label( string $attribute ) : string {
        $label  = ucfirst( strtolower( str_replace( '_', '', $attribute ) ) );
        $labels = $this->attribute_labels();

        if ( empty( $labels[ $attribute ] ) ) {
            return $label;
        }

        return $labels[ $attribute ];
    }

}
