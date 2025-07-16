<?php

namespace wpmvc\validators;

class Email_Validator extends Validator {

    public $message = 'Value must an email address.';

    public function validate( $value ): bool {
        if ( empty( $value ) ) {
            return true;
        }

        return is_email( $value );
    }

}
