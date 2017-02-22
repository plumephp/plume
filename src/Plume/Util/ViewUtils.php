<?php

namespace Plume\Util;

class ViewUtils {
	
	public static function echo($data){
        echo $data;
    }

    public static function safeEcho($data){
    	echo isset($data) ? $data : '';
    }
}
