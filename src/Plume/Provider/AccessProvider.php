<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2019/2/19
 * Time: 16:46
 */

namespace Plume\Provider;


class AccessProvider extends Provider {

    public function request($requestId){
        if($this->accessLogStatus() == 0){
            return;
        }
        $plumeAccess = $this->getConfigValue('plume_access');
        /**
         * @var $redis \Redis
         */
        $redis = $this->provider('redis')->connectByConfig($plumeAccess['redis']);
        $value = $plumeAccess['project_name'].':'.$requestId;
        $redis->lPush('plume_access_list', $value);
        $redis->hMset($value, array(
            'project_name' => $plumeAccess['project_name'],
            'api_url' => $_SERVER['REQUEST_URI'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'req_data' => json_encode($_REQUEST, JSON_UNESCAPED_UNICODE),
            'req_dt' => date('Y-m-d H:i:s'),
            'req_time' => $this->msecTime(),
            'res_data' => '',
            'res_dt' => '',
            'res_time' => ''
        ));
    }

    public function response($requestId, $responseData){
        if($this->accessLogStatus() == 0){
            return;
        }
        $plumeAccess = $this->getConfigValue('plume_access');
        $this->provider('redis')->connectByConfig($plumeAccess['redis'])
            ->hMset(
                $plumeAccess['project_name'].':'.$requestId,
                array(
                    'res_data' => json_encode($responseData, JSON_UNESCAPED_UNICODE),
                    'res_dt' => date('Y-m-d H:i:s'),
                    'res_time' => $this->msecTime()
                )
            );
    }

    /**
     * 访问日志状态（1表示开启记录访问日志，0表示未开启记录访问日志）
     * @return int
     */
    public function accessLogStatus(){
        $plumeAccess = $this->getConfigValue('plume_access');
        if(isset($plumeAccess['project_name']) && !empty($plumeAccess['project_name'])){
            return 1;
        }
        return 0;
    }

    /**
     * 获取当前时间戳（单位毫秒）
     * @return string
     */
    public function msecTime() {
        list($msec, $sec) = explode(' ', microtime());
        return sprintf('%.0f', ($msec + $sec) * 1000);
    }
}