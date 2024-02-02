<?php

namespace wpmvc\base;

abstract class App extends Component {

    private $config = array();

    /**
     * @var Request
     */
    public $request;

    public function __construct( array $config = array() ) {
        $this->set_config( $config );

        $this->setup_components();
    }

    /**
     * @param array $config
     * @return void
     */
    private function set_config( array $config = array() ) {
        $this->config = array_merge( array(
            'name'       => '',
            'components' => array(),
        ), $config );
    }

    private function setup_components() {
        if ( empty( $this->config['components'] ) ) {
            return;
        }

        foreach ( $this->config['components'] as $component => $params ) {
            $this->{ $component } = new $params['class']( $params );
        }
    }

}
