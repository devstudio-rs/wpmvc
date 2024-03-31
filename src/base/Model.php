<?php

namespace wpmvc\base;

use wpmvc\validators\Validator;

abstract class Model extends Component {

    protected $errors = array();

    /**
     * Set models rules that will apply before saving attribute values.
     *
     * Example of usage:
     *
     * ```php
     * array(
     *   array( array( 'title', 'email' ), 'required', array(
     *     'message' => __( 'Field is required!' ),
     *   ) ),
     *   array( 'email', 'email' ),
     *   array( 'number', 'number' ),
     * )
     * ```
     * @return array
     */
    public function rules() : array {
        return array();
    }

    /**
     * Validate modal attributes based on the define rules.
     *
     * @return bool
     */
    public function validate() : bool {
        $rules = $this->rules();

        if ( empty( $rules ) ) {
            return true;
        }

        foreach ( $rules as $rule ) {
            $this->validate_rule( $rule );
        }

        if ( ! empty( $this->errors ) ) {
            return false;
        }

        return true;
    }

    /**
     * Validate single rule.
     *
     * @param array $rule
     * @return void
     */
    private function validate_rule( array $rule ) {
        $attributes = ! empty( $rule[0] ) ? $rule[0] : null;
        $validator  = ! empty( $rule[1] ) ? $rule[1] : null;
        $args		= ! empty( $rule[2] ) ? $rule[2] : array();

        if ( ! is_array( $attributes ) ) {
            $attributes = array( $attributes );
        }

        if ( empty( $attributes ) || empty( $validator ) ) {
            return;
        }

        foreach ( $attributes as $attribute ) {
            $this->validate_attribute( $attribute, $validator, $args );
        }
    }

    /**
     * Validate specific model attribute based on validator.
     * Use $args to overwrite validator attributes.
     *
     * @param string $attribute
     * @param $validator
     * @param array $args
     * @return mixed
     */
    private function validate_attribute( string $attribute, $validator, array $args = array() ) {
        if ( is_callable( $validator ) ) {
            return call_user_func( $validator, $this );
        }

        $validator_class = Validator::get_validator_class( $validator );
        $validator       = new $validator_class();

        if ( ! $validator instanceof Validator ) {
            return false;
        }

        $validator->init();

        return $validator->validate_attribute( $this, $attribute, $args );
    }

    /**
     * Get model errors.
     *
     * @return array
     */
    public function get_errors() : array {
        return $this->errors;
    }

    /**
     * Set attribute error message.
     *
     * @param string $attribute
     * @param string $message
     * @return void
     */
    public function add_error( string $attribute, string $message ) {
        $this->errors[] = array(
            'attribute' => $attribute,
            'message'	=> $message,
        );
    }

}
