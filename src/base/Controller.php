<?php

namespace wpmvc\base;

abstract class Controller extends Component {

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $title;

    public function before_action() {}

}
