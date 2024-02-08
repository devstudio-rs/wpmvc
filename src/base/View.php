<?php

namespace wpmvc\base;

abstract class View extends Component {

    public static function render( string $view, array $params = array() ) {
        global $wp_query;

        $wp_query->query_vars = array_merge(
            $wp_query->query_vars,
            $params
        );

        ob_start();

        get_template_part( $view );

        return ob_get_clean();
    }

}
