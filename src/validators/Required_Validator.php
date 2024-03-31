<?php

namespace wpmvc\validators;

class Required_Validator extends Validator {

    public $message = 'Field is required.';

    public function validate( $value ): bool {
        return ! empty( $value );
    }

}
