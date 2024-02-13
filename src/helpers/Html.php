<?php

namespace wpmvc\helpers;

use wpmvc\models\Post_Model;

class Html {

    /**
     * @param string $name
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function tag( string $name, string $content = '', array $options = array() ) : string {
        $attributes = array();

        foreach ( $options as $attribute => $value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $value );
        }

        return sprintf( '<%s %s>%s</%s>', $name, implode( ' ', $attributes ), $content, $name );
    }

    /**
     * @param string $type
     * @param string $name
     * @param array $options
     * @return string
     */
    public static function input( string $type = 'text', string $name = '', array $options = array() ) : string {
        $attributes = array(
            sprintf( 'type="%s"', $type ),
            sprintf( 'name="%s"', $name ),
        );

        foreach ( $options as $attribute => $value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $value );
        }

        return sprintf( '<input %s>', implode( ' ', $attributes ) );
    }

    /**
     * @param Post_Model $model
     * @param string $attribute
     * @param string $type
     * @param array $options
     * @return string
     */
    public static function active_input( Post_Model $model, string $attribute, string $type = 'text', array $options = array() ) : string {
        $name = sprintf( '%s[%s]', $model->get_class_name(), $attribute );

        $options['value'] = $model->get_attribute( $attribute );

        return static::input( $type, $name, $options );
    }

}
