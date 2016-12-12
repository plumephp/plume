<?php

namespace Plume\Provider;

/*
- cache的局限性
1. 不解决静态资源的cache问题
2. request类型cache对view和API同样有效
3. API的cache对JSON类型返回数据的header设置不友好
4. 使用排除配置针对性选择cache是个不错的选择
5. DB类型cache只针对单表数据
*/
class CacheProvider extends Provider{

    //从cache配置中获取路由的过期时间
    private function getExpireTime(){
        $config = $this->plume();
        return isset($config['plume.cache.config'][$config['plume.request.path']]) ? $config['plume.cache.config'][$config['plume.request.path']] : $config['plume.cache.expire'];
    }

    ////是否启用cache或过期时间为零，则不cache
    private function isCache($expireTime, $cacheType){
        $isCacheKey = 'plume.cache.'.$cacheType;
        return $this->plume($isCacheKey) && $expireTime !== 0;
    }

    private function zip($cacheFunc, $cacheType){
        if($cacheType === 'db'){
            return serialize($cacheFunc());
        }
        if($cacheType === 'request'){
            ob_start();
            $cacheFunc();
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
        return $cacheFunc();
    }

    private function unzip($value, $cacheType){
        if($cacheType === 'db'){
            return unserialize($value);
        }
        if($cacheType === 'request'){
            echo $value;
        }
        return $value;
    }

    private function getCacheKey($cacheType, $cacheFeed){
        $key = 'plume.cache.';
        if($cacheType === 'db'){
            $key .= $cacheType.'.';
            //TODO:1.处理单表多条件 2.多表
            $key .= md5($cacheFeed);
        }
        if($cacheType === 'request'){
            $key .= $cacheType.'.';
            $key .= md5($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:''));
        }
        $log = $this->provider('log')->debug('CacheProvider', 'CacheKey is '.$key);
        return $key;
    }

    public function cacheClear($cacheType, $cacheFeed = ''){
        if(!$this->plume('plume.cache.db')){
            return;
        }
        $cacheKey = $this->getCacheKey($cacheType, $cacheFeed);
        $this->provider('log')->debug('CacheProvider db', 'cache clear key is '.$cacheKey);
        $con = $this->provider('redis')->connect();
        $con->del($cacheKey);
    }

    public function cacheWith($cacheFunc, $cacheType, $cacheFeed = ''){
        $log = $this->provider('log');

        $expireTime = $this->getExpireTime();
        $isCache = $this->isCache($expireTime, $cacheType);
        $log->debug('CacheProvider', $cacheType.' : isCache is '.(int)$isCache);
        $log->debug('CacheProvider', 'expireTime is '.$expireTime);
        if(!$isCache){
            return $cacheFunc();
        }
        $cacheKey = $this->getCacheKey($cacheType, $cacheFeed);
        $con = $this->provider('redis')->connect();
        $cachedVal = $con->get($cacheKey);
        $log->debug('CacheProvider', 'cached value is '.substr($cachedVal, 0, 500));
        if(!$cachedVal){
            $cachedVal = $this->zip($cacheFunc, $cacheType);
            $con->set($cacheKey,$cachedVal);
            $con->expireAt($cacheKey, time() + $expireTime);
            $log->debug('CacheProvider', 'new value is '.substr($cachedVal, 0, 500));
        }else{
            $this->plume('plume.request.path', $cacheType.'.cache');
        }
        return $this->unzip($cachedVal, $cacheType);
    }
}