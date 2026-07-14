<?php

namespace wpmvc\base;

class View extends Component {

    /**
     * Render a view and return its output. `$params` are exposed as
     * variables inside the template (via `query_vars`).
     *
     * A relative view resolves through `get_template_part()` — the theme
     * flow, child-theme overrides included. An absolute path loads that
     * file directly, which is how plugins render their own views:
     *
     *     View::render( 'views/test' );                          // theme
     *     View::render( WPMVC::alias( '@root/views/test' ) );    // plugin
     *     View::render( __DIR__ . '/views/test' );               // plugin
     *
     * The `.php` extension is appended when missing. A non-existent
     * absolute view renders nothing, mirroring `get_template_part()`.
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public static function render( string $view, array $params = array() ) {
        global $wp_query;

        $wp_query->query_vars = array_merge(
            $wp_query->query_vars,
            $params
        );

        ob_start();

        if ( ! path_is_absolute( $view ) ) {
            get_template_part( $view );

            return ob_get_clean();
        }

        $template = substr( $view, -4 ) === '.php' ? $view : $view . '.php';

        if ( file_exists( $template ) ) {
            load_template( $template, false );
        }

        return ob_get_clean();
    }

}
