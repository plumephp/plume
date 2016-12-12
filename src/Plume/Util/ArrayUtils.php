<?php

namespace Plume\Util;

class ArrayUtils {
	
	public static function getValue(array $arr, $key, $defaultValue = null){
        return (isset($arr[$key]) && !empty($arr[$key])) ? $arr[$key] : $defaultValue;
    }
}
