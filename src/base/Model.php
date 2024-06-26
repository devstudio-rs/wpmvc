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
        if ( empty( $this->errors ) ) {
            return array();
        }

        $grouped_errors = array();

        foreach ( $this->errors as $error ) {
            $existing_index = array_search(
                $error['attribute'],
                array_column( $grouped_errors, 'attribute' )
            );

            if ( $existing_index === false ) {
                $grouped_errors[] = array(
                    'attribute' => $error['attribute'],
                    'messages'  => array( $error['message'] ),
                );
            }

            if ( $existing_index !== false ) {
                $grouped_errors[ $existing_index ]['messages'][] = $error['message'];
            }
        }

        return $grouped_errors;
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

    public function to_response() {
        $errors = $this->get_errors();

        if ( empty( $errors ) ) {
            wp_send_json_success();
        }

        wp_send_json_error( $errors );
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function load( array $attributes = array() ) : bool {
        $loaded = false;

        if ( empty( $attributes ) ) {
            return false;
        }

        $class_name = $this->get_class_name();

        if ( empty( $attributes[ $class_name ] ) ) {
            return false;
        }

        $model_attributes = $attributes[ $class_name ];

        foreach ( $this->get_attributes() as $attribute => $value ) {
            if ( ! isset( $model_attributes[ $attribute ] ) ) {
                continue;
            }

            $this->set_attribute( $attribute, $model_attributes[ $attribute ] );

            $loaded = true;
        }

        return $loaded;
    }

}
