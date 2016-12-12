<?php

namespace Plume\Util;

class Guid {

    /**
     * 32位GUID生成方法
     * 自定义guid生成规则: uniqid(prefix + mt_rand , true)
     * 碰撞测试：用for循环获取了100万个guid，没有出现重复情况
     * $prefix 前缀混淆串，能有效增加GUID的唯一性。一般传入当前记录的唯一归属ID、所属类型等
     *
     * @param string $prefix
     * @return string
     */
    public static function get($prefix=null){
        return self::create_guid_md5($prefix);
    }

    /**
     * 32位GUID生成方法，算法为md5
     * 自定义guid生成规则: uniqid(prefix + mt_rand , true)
     * 碰撞测试：用for循环获取了100万个guid，没有出现重复情况
     * $prefix 前缀混淆串，能有效增加GUID的唯一性。一般传入当前记录的唯一归属ID、所属类型等
     *
     * @param string $prefix
     * @return string
     */
    public static function create_guid_md5($prefix=null){
        // mt_rand() 马特赛特旋转演算法，可以快速产生高质量的伪随机数，修正了古老随机数产生算法的很多缺陷
        return strtolower(md5(uniqid($prefix . mt_rand(), true)));
    }

    //sha265算法的安全值，用于计算字符串的摘要
    private static $_secret4sha265 = '@sha265_secret_guid_plume#';

    /**
     * 64位GUID生成方法，算法为sha256
     * 自定义guid生成规则: uniqid(prefix + mt_rand , true)
     * 碰撞测试：用for循环获取了100万个guid，没有出现重复情况
     * $prefix 前缀混淆串，能有效增加GUID的唯一性。一般传入当前记录的唯一归属ID、所属类型等
     * @param string $prefix
     * @return string
     */
    public static function create_guid_sha256($prefix=null){
        // mt_rand() 马特赛特旋转演算法，可以快速产生高质量的伪随机数，修正了古老随机数产生算法的很多缺陷
        return strtolower(hash_hmac("sha256",uniqid($prefix . mt_rand(), true), self::$_secret4sha265));
    }
}