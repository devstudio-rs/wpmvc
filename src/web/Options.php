<?php

namespace wpmvc\web;

use wpmvc\App;
use wpmvc\base\Component;
use wpmvc\models\Model;

class Options extends Component {

    const TYPE_STRING = 'string';
    const TYPE_INT    = 'int';
    const TYPE_FLOAT  = 'float';
    const TYPE_BOOL   = 'bool';

    public $id     = 'options';
    public $label;
    public $config = array();

    public function __construct( $params = array() ) {
        $this->set_attributes( $params );

        add_action( 'admin_init', array( $this, 'setup_options' ) );
        add_action( 'admin_menu', array( $this, 'setup_options_pages' ) );
    }

    /**
     * @param string $name
     * @param mixed $default
     * @param string|null $type
     * @return false|mixed|null
     */
    public function get( string $name, $default = null, string $type = null ) {
        $value = get_option( $name, $default );

        if ( $type === static::TYPE_STRING ) {
            return (string) $value;
        }

        if ( $type === static::TYPE_INT ) {
            return (int) $value;
        }

        if ( $type === static::TYPE_FLOAT ) {
            return (float) $value;
        }

        if ( $type === static::TYPE_BOOL ) {
            return (bool) $value;
        }

        return $value;
    }

    /**
     * @param string $name
     * @param $value
     * @return bool
     */
    public function set( string $name, $value ) : bool {
        return update_option( $name, $value );
    }

    public function setup_options() {
        $this->config = apply_filters( 'wpmvc_options', $this->config );

        if ( empty( $this->config ) ) {
            return;
        }

        foreach ( $this->config as &$option ) {
            foreach ( $option['items'] as &$item ) {
                $item = apply_filters( 'wpmvc_options_' . $item['name'], $item );

                register_setting( $option['id'], $item['name'] );
            }
        }

        unset( $option, $item );
    }

    public function setup_options_pages() {
        if ( empty( $this->config ) ) {
            return;
        }

        add_menu_page(
            $this->label,
            $this->label,
            'manage_options',
            $this->id
        );

        foreach ( $this->config as $index => $option ) {
            add_submenu_page(
                $this->id,
                $option['label'],
                $option['label'],
                'manage_options',
                ( $index === 0 ? $this->id : $option['id'] ),
                array( $this, 'setup_options_template' )
            );
        }
    }

    /**
     * Setup theme options template.
     *
     * @return void
     */
    public function setup_options_template() {
        $page_slug    = App::$app->request->get( 'page' );
        $option_index = array_search( $page_slug, array_column( $this->config, 'id' ) );
        $option_index = ! empty( $option_index ) ? $option_index : 0;

        $options = $this->config[ $option_index ];
        $model   = new Model();

        foreach ( $options['items'] as &$item ) {
            $default_value = ! empty( $item['default'] ) ? $item['default'] : null;

            $model->{ $item['name'] } = get_option( $item['name'], $default_value );

            register_setting( $options['id'], $item['name'] );
        }

        unset( $item );

        load_template( sprintf( '%s/views/theme-options.php', dirname( __DIR__ ) ), true, array(
            'model'   => $model,
            'options' => $options,
        ) );
    }

}
