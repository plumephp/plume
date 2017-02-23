<?php

namespace Plume\Util;

class ViewUtils {
	
	public static function write($data){
        echo $data;
    }

    public static function safeWrite($data){
    	echo isset($data) ? $data : '';
    }
}
