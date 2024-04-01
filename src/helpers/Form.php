<?php

namespace wpmvc\helpers;

use wpmvc\base\Model;
use wpmvc\models\Post_Model;

class Form extends \wpmvc\base\Component {

    /**
     * @var string
     */
    public $template = "{label}{input}";

    /**
     * @var string[]
     */
    public $field_options = array( 'class' => 'form-group' );

    /**
     * @var string[]
     */
    public $input_options = array( 'class' => 'form-control' );

    /**
     * @var null|Post_Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var array
     */
    public $parts = array();

    public function __toString() {
        wp_enqueue_script( 'wpmvc-form' );

        return $this->render();
    }

    /**
     * @return string
     */
    public function render() : string {
        $output = $this->template;

        if ( empty( $this->parts['{label}'] ) ) {
            $this->parts['{label}'] = Html::tag( 'label', $this->model->get_attribute_label( $this->attribute ), array(
                'for'   => $this->attribute,
                'class' => 'form-label',
            ) );
        }

        foreach ( $this->parts as $part => $element ) {
            $output = str_replace( $part, $element, $output );
        }

        $this->field_options = array_merge( $this->field_options, array(
            'data-attribute' => $this->attribute,
        ) );

        return Html::tag( 'div', $output, $this->field_options );
    }

    /**
     * @param array $options
     * @return string
     */
    public static function start( array $options = array() ) : string {
        $options = array_merge( array(
            'class'  => '',
            'action' => '',
            'method' => 'post',
        ), $options );

        $options['class'] = trim( implode( ' ', array_merge(
            explode( ' ', $options['class'] ),
            array( 'wpmvc-form' )
        ) ) );

        $attributes = array();

        foreach ( $options as $attribute => $value ) {
            $attributes[] = sprintf( '%s="%s"', $attribute, $value );
        }

        return sprintf( '<form %s>', implode( ' ', $attributes ) );
    }

    /**
     * @return string
     */
    public static function end() : string {
        return '</form>';
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return static
     */
    public static function field( Model $model, string $attribute, array $options = array() ) : self {
        $field = new static();

        $field->set_attribute( 'model',     $model );
        $field->set_attribute( 'attribute', $attribute );

        $field->field_options = array_merge( $field->field_options, $options );

        return $field;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function label( string $label ) : self {
        if ( empty( $label ) ) {
            return $this;
        }

        $this->parts['{label}'] = Html::tag( 'label', $label, array(
            'for'   => $this->attribute,
            'class' => 'form-label',
        ) );

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function input_text( array $options = array() ) : self {
        $this->parts['{input}'] = Html::active_input(
            $this->model,
            $this->attribute,
            'text',
            array_merge(
                $this->input_options,
                array( 'id' => $this->attribute ),
                $options
            )
        );

        return $this;
    }

    public function select( array $items = array(), array $options = array() ) : self {
        $this->parts['{input}'] = Html::active_select(
            $this->model,
            $this->attribute,
            $items,
            array_merge(
                $this->input_options,
                array( 'id' => $this->attribute ),
                $options
            )
        );

        return $this;
    }

}
