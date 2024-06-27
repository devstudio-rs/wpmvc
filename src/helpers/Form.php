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
     * @var array
     */
    public $field_options = array();

    /**
     * @var array
     */
    public $input_options = array();

    /**
     * @var array
     */
    public $label_options = array();

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

        $this->label_options = array_merge( array(
            'for'   => $this->attribute,
            'class' => 'form-label',
        ), $this->label_options );

        if ( empty( $this->parts['{label}'] ) ) {
            $this->parts['{label}'] = Html::tag(
                'label',
                $this->model->get_attribute_label( $this->attribute ),
                $this->label_options
            );
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

        // Make option to change template trough field options.
        if ( ! empty( $options['template'] ) ) {
            $field->set_attribute( 'template', $options['template'] );
        }

        $field->field_options = array_merge( $field->field_options, $options );

        return $field;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function label( $label ) {
        if ( $label === false ) {
            $this->template = str_replace( '{label}', '', $this->template );

            return $this;
        }

        $this->label_options = array_merge( array(
            'for'   => $this->attribute,
            'class' => 'form-label',
        ), $this->label_options );

        $this->parts['{label}'] = Html::tag( 'label', $label, $this->label_options );

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function input_text( array $options = array() ) {
        if ( empty( $this->field_options ) ) {
            $this->field_options = array( 'class' => 'form-group' );
        }

        if ( empty( $this->input_options ) ) {
            $this->input_options = array( 'class' => 'form-control' );
        }

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

    /**
     * @param array $items
     * @param array $options
     * @return $this
     */
    public function select( array $items = array(), array $options = array() ) {
        if ( empty( $this->field_options ) ) {
            $this->field_options = array( 'class' => 'form-group' );
        }

        if ( empty( $this->input_options ) ) {
            $this->input_options = array( 'class' => 'form-control' );
        }

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

    /**
     * @param array $options
     * @return $this
     */
    public function checkbox( array $options = array() ) {
        if ( empty( $this->field_options ) ) {
            $this->field_options = array( 'class' => 'form-check' );
        }

        if ( empty( $this->input_options ) ) {
            $this->input_options = array( 'class' => 'form-check-input' );
        }

        if ( empty( $this->label_options ) ) {
            $this->label_options = array( 'class' => 'form-check-label' );
        }

        $this->parts['{input}'] = Html::active_checkbox(
            $this->model,
            $this->attribute,
            array_merge(
                $this->input_options,
                array( 'id' => $this->attribute ),
                $options
            )
        );

        return $this;
    }

}
