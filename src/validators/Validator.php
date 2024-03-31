<?php

namespace wpmvc\validators;

use wpmvc\base\Component;

abstract class Validator extends Component {

    /**
     * Built-in validators.
     *
     * @var string[]
     */
    public static $validators = array(
        'required'  => 'wpmvc\validators\Required_Validator',
        'number'    => 'wpmvc\validators\Number_Validator',
        'email'     => 'wpmvc\validators\Email_Validator',
    );

    /**
     * Error message.
     *
     * @var string
     */
    public $message;

    public function init() {}

    /**
     * @param $value
     * @return bool
     */
    public function validate( $value ) : bool {
        return true;
    }

    /**
     * @param $model
     * @param string $attribute
     * @param array $args
     * @return bool
     */
    public function validate_attribute( $model, string $attribute, array $args = array() ) : bool {
        $this->set_attributes( $args );

        $value = $model->get_attribute( $attribute );

        if ( $this->validate( $value ) ) {
            return true;
        }

        $model->add_error( $attribute, $this->message );

        return false;
    }

    /**
     * Get class of built-in validators.
     *
     * @param string $validator
     * @return string
     */
    public static function get_validator_class( string $validator ) : string {
        if ( empty( static::$validators[ $validator ] ) ) {
            return $validator;
        }

        return static::$validators[ $validator ];
    }

}
