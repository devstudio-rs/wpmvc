<?php

namespace wpmvc\helpers;

class Array_Helper {

    public static function map( array $array, string $key, string $value ) : array {
        $items = array();

        foreach ( $array as $item ) {
            // Convert object to array.
            $item = (array) $item;

            $items[ $item[ $key ] ] = $item[ $value ];
        }

        return $items;
    }

}
