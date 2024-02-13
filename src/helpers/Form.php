<?php

namespace wpmvc\helpers;

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
     * @var array
     */
    public $parts = array();

    public function __toString() {
        return $this->render();
    }

    /**
     * @return string
     */
    public function render() : string {
        $output = $this->template;

        foreach ( $this->parts as $part => $element ) {
            $output = str_replace( $part, $element, $output );
        }

        return Html::tag( 'div', $output, $this->field_options );
    }

    /**
     * @param array $options
     * @return string
     */
    public static function start( array $options = array() ) : string {
        $options = array_merge( array(
            'action' => '',
            'method' => 'post',
        ), $options );

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
     * @param array $options
     * @return static
     */
    public static function field( array $options = array() ) : self {
        $field = new static();

        $field->field_options = array_merge( $field->field_options, $options );

        return $field;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function label( string $label ) : self {
        $this->parts['{label}'] = Html::tag( 'label', $label );

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function text_input( array $options = array() ) : self {
        $this->parts['{input}'] = Html::input( 'text', 'test', array_merge( $this->input_options, $options ) );

        return $this;
    }

}
