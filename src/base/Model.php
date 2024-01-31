<?php

namespace wpmvc\base;

abstract class Model {

    public function get_attributes() {
        return get_object_vars( $this );
    }

}
