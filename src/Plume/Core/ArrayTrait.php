<?php

namespace Plume\Core;

trait ArrayTrait{

    public function offsetExists($index){
        return isset($this->context[$index]);
    }
    
    public function offsetSet($index, $value){
        $this->context[$index] = $value;
    }

    public function offsetGet($index){
        return (isset($this->context[$index])) ? $this->context[$index] : '';
    }
    
    public function offsetUnset($index){
        unset($this->context[$index]);
    }
	
}