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

        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     * Init view.
     *
     * @return void
     */
    public function init() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    }

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

}
