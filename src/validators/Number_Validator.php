<?php

namespace wpmvc\validators;

class Number_Validator extends Validator {

    public $message = 'Value must be a number.';

    public function validate( $value ): bool {
        return is_numeric( $value );
    }

}
