<?php

namespace wpmvc\validators;

class Email_Validator extends Validator {

    public $message = 'Value must an email address.';

    public function validate( $value ): bool {
        return is_email( $value );
    }

}
