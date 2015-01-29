<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 1/20/15
 * Time: 12:39 PM
 */

class Arrayhelper {
    public function indexedArray($array, $key)
    {
        if (!isset($key)) {
            return $array;
        }
        $result = array();
        foreach ($array as $item) {
            $result[$item[$key]] = $item;
        }
        return $result;
    }
}