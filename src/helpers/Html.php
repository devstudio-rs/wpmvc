<?php

namespace wpmvc\helpers;

use wpmvc\base\Model;
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
     * @param Model $model
     * @param string $attribute
     * @param string $type
     * @param array $options
     * @return string
     */
    public static function active_input( Model $model, string $attribute, string $type = 'text', array $options = array() ) : string {
        $name = sprintf( '%s[%s]', $model->get_class_name(), $attribute );

        $options['value'] = $model->get_attribute( $attribute );

        return static::input( $type, $name, $options );
    }

    /**
     * @param string $name
     * @param array|bool|null|string $selection
     * @param array $items
     * @param array $options
     * @return string
     */
    public static function select( string $name = '', $selection = null, array $items = array(), array $options = array() ) : string {
        if ( ! empty( $options['multiple'] ) ) {
            $name = sprintf( '%s[]', $name );
        }

        $attributes = array(
            sprintf( 'name="%s"', $name ),
        );

        foreach ( $options as $attribute => $value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $value );
        }

        $select_options = array(
            sprintf( '<option value="">%s</option>',
                ( ! empty( $options['prompt'] ) ? $options['prompt'] : __( 'Please select...' ) ) ),
        );

        $selection = is_array( $selection ) ? $selection : array( $selection );

        if ( ! empty( $items ) ) {
            foreach ( $items as $value => $label ) {
                $select_options[] = sprintf( '<option value="%s" %s>%s</option>',
                    $value,
                    ( in_array( $value, $selection ) ? 'selected' : '' ),
                    $label
                );
            }
        }

        return sprintf( '<select %s>%s</select>',
            implode( ' ', $attributes ),
            implode( '', $select_options )
        );
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function active_select( Model $model, string $attribute, array $items = array(), array $options = array() ) : string {
        $name = sprintf( '%s[%s]', $model->get_class_name(), $attribute );

        return static::select( $name, $model->get_attribute( $attribute ), $items, $options );
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public static function textarea( string $name = '', string $value = null, array $options = array() ) : string {
        $attributes = array(
            sprintf( 'name="%s"', $name ),
        );

        foreach ( $options as $attribute => $attribute_value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $attribute_value );
        }

        return sprintf( '<textarea %s>%s</textarea>', implode( ' ', $attributes ), $value );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return string
     */
    public static function checkbox( $name, $value, $checked = false, $options = array() ) {
        $attributes = array(
            sprintf( 'type="%s"', 'checkbox' ),
            sprintf( 'name="%s"', $name ),
            sprintf( 'value="%s"', $value ),
        );

        if ( $checked ) {
            $options['checked'] = 'checked';
        }

        foreach ( $options as $attribute => $value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $value );
        }

        return Html::tag( 'input', '', array(
            'type'  => 'hidden',
            'name'  => $name,
            'value' => 0
        ) ) . sprintf( '<input %s>', implode( ' ', $attributes ) );
    }

    /**
     * @param \wpmvc\models\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function active_checkbox( $model, $attribute, $options = array() ) {
        $name    = sprintf( '%s[%s]', $model->get_class_name(), $attribute );
        $checked = (int) $model->get_attribute( $attribute );

        return static::checkbox( $name, 1, $checked, $options );
    }

}
