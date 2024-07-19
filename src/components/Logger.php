<?php

namespace wpmvc\components;

use wpmvc\base\App;
use wpmvc\base\Component;

/**
 * Class Logger
 *
 * @since 1.0.0
 * @package wpmvc\components
 *
 * @property string $directory
 * @property int $permissions
 * @property int $size
 */

class Logger extends Component {

    const TYPE_INFO     = 'info';
    const TYPE_ERROR    = 'error';
    const TYPE_WARNING  = 'warning';

    public $directory   = '@upload.basedir/logs';
    public $group       = 'app';
    public $permissions = 0755;
    public $size        = 0.1;

    public function __construct( $attributes = array() ) {
        $this->set_attributes( $attributes );
    }

    /**
     * @param string $message
     * @param string $type
     * @param string $group
     * @return false|int
     */
    public function log( $message, $type, $group = null ) {
        $group     = $group ?? $this->group;
        $directory = App::alias( $this->directory );
        $filename  = sprintf( '%s/%s.log', $directory, $group );
        $message   = sprintf( '[%s] %s: %s', date( 'Y-m-d H:i:s' ), $type, $message ) . PHP_EOL;

        if ( ! file_exists( $directory ) ) {
            @mkdir( $directory, $this->permissions, true );
        }

        $this->check_file_size_limit( $filename );

        return @file_put_contents( $filename, $message, FILE_APPEND );
    }

    private function check_file_size_limit( $filename ) {
        $file_size = @filesize( $filename );

        if ( empty( $file_size ) ) {
            return false;
        }

        // Convert MB to Bytes.
        $file_size_limit = $this->size * 1024 * 1024;

        if ( $file_size < $file_size_limit ) {
            return false;
        }

        $new_filename_temp = sprintf( '%s/%s-{i}.%s',
            pathinfo( $filename, PATHINFO_DIRNAME ),
            pathinfo( $filename, PATHINFO_FILENAME ),
            pathinfo( $filename, PATHINFO_EXTENSION )
        );

        $increment = 1;

        while ( file_exists( strtr( $new_filename_temp, array( '{i}' => $increment ) ) ) ) {
            $increment ++;
        }

        $new_filename = strtr( $new_filename_temp, array( '{i}' => $increment ) );

        return rename( $filename, $new_filename );
    }

}
