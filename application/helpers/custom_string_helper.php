<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('get_array_value_do_ucwords')) {

    function get_array_value_do_ucwords($array = array()) {
        foreach ($array as $key => $value) {
            $array[$key] = ucwords(strtolower($value));
        }
        return $array;
    }

}