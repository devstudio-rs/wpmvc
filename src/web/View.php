<?php

namespace wpmvc\web;

use wpmvc\App;

class View extends \wpmvc\base\View {

    public $config;

    /**
     * @param array $config
     */
    public function __construct( array $config ) {
        $this->config = $config;

        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

        add_filter( 'style_loader_src',   array( $this, 'style_script_loader' ), 20 );
        add_filter( 'script_loader_src',  array( $this, 'style_script_loader' ), 20 );
    }

    /**
     * Init view.
     *
     * @return void
     */
    public function init() {}

    /**
     * Register styles from the config.
     *
     * @return void
     */
    public function register_styles() {
        if ( empty( $this->config['assets'] ) ) {
            return;
        }

        if ( empty( $this->config['assets']['css'] ) ) {
            return;
        }

        $theme_version = wp_get_theme()->get( 'Version' );

        foreach ( $this->config['assets']['css'] as $asset ) {
            $asset = array_merge( array(
                'handle' => '',
                'src'    => '',
                'deps'   => array(),
                'ver'    => $theme_version,
                'media'  => 'all',
            ), $asset );

            wp_enqueue_style( $asset['handle'], App::alias( $asset['src'] ), $asset['deps'], $asset['ver'], $asset['media'] );
        }
    }

    /**
     * Register scripts from the config.
     *
     * @return void
     */
    public function register_scripts() {
        if ( empty( $this->config['assets'] ) ) {
            return;
        }

        if ( empty( $this->config['assets']['js'] ) ) {
            return;
        }

        $theme_version = wp_get_theme()->get( 'Version' );

        foreach ( $this->config['assets']['js'] as $asset ) {
            $asset = array_merge( array(
                'handle' => '',
                'src'    => '',
                'deps'   => array(),
                'ver'    => $theme_version,
                'media'  => 'all',
            ), $asset );

            wp_enqueue_script( $asset['handle'], App::alias( $asset['src'] ), $asset['deps'], $asset['ver'], $asset['media'] );
        }
    }

    /**
     * @param string $src
     * @return string
     */
    public function style_script_loader( string $src ) : string {
        $clean_src = remove_query_arg( 'ver', $src );
        $path      = wp_parse_url( $src, PHP_URL_PATH );

        if ( $modified_time = @filemtime( untrailingslashit( ABSPATH ) . $path ) ) {
            return add_query_arg( 'v', sha1( $modified_time ), $clean_src );
        }

        return $src;
    }

}
