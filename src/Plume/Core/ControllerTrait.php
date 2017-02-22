<?php

namespace Plume\Core;

trait ControllerTrait{
    
    protected $context = array(
            'response' => array(
                'data' => null,
                'api' => false,
                'error' => false,
                'view' => null,
                'view_exception' => null,
            )
        );

    protected function msg($code, $msg){
        $this->context['response']['data'] = json_encode(array('code' => $code, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
        return $this;
    }

    protected function result($data){
        $this->context['response']['data'] = $data;
        return $this;
    }

    protected function view_exception($view){
        $this->context['response']['view_exception'] = $view;
        return $this;
    }

    protected function view($view){
        $this->context['response']['view'] = $view;
        return $this;
    }

    protected function error(){
        $this->context['response']['error'] = true;
        return $this;
    }

    protected function json(){
        $this->context['response']['data'] = json_encode($this->context['response']['data'], JSON_UNESCAPED_UNICODE);
        return $this;
    }
    
    protected function api(){
        $this->context['response']['api'] = true;
        return $this;
    }

    protected function response(){
        return $this->context['response'];
    }
}