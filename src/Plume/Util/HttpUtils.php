<?php

namespace Plume\Util;

/**
 * HTTP相关的功能工具
 */

class HttpUtils {

	/**
	 * 发起GET请求
	 *
	 * @param string $url
	 * @return string content
	 */
	public static function http_get($url, $timeOut = 5, $connectTimeOut = 5) {
		$oCurl = curl_init ();
		if (stripos ( $url, "http://" ) !== FALSE || stripos ( $url, "https://" ) !== FALSE) {
			curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, FALSE );
		}
		curl_setopt($oCurl, CURLOPT_URL, $url );
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $connectTimeOut);
		$sContent = curl_exec ( $oCurl );
		$aStatus = curl_getinfo ( $oCurl );
        $error = curl_error( $oCurl );
		curl_close ( $oCurl );
		if (intval ( $aStatus ["http_code"] ) == 200) {
			return array(
					'status' => true,
					'content' => $sContent,
					'code' => $aStatus ["http_code"],
			);
		} else {
			return array(
					'status' => false,
					'content' => json_encode(array("error" => $error, "url" => $url)),
					'code' => $aStatus ["http_code"],
			);
		}
	}

	/**
	 * 发起POST请求
	 *
	 * @param string $url
	 * @param array $param
	 * @return string content
	 */
	public static function http_post($url, $param, $timeOut = 5, $connectTimeOut = 5) {
		$oCurl = curl_init ();
		if (stripos ( $url, "http://" ) !== FALSE || stripos ( $url, "https://" ) !== FALSE) {
			curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, false );
		}
		if (is_string ( $param )) {
			$strPOST = $param;
		} else {
			$aPOST = array ();
			foreach ( $param as $key => $val ) {
				$aPOST [] = $key . "=" . urlencode ( $val );
			}
			$strPOST = join ( "&", $aPOST );
		}
		curl_setopt($oCurl, CURLOPT_URL, $url );
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST, true );
		curl_setopt($oCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST );
		curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $connectTimeOut);
		$sContent = curl_exec ($oCurl );
		$aStatus = curl_getinfo ($oCurl );
        $error = curl_error($oCurl );
		curl_close ($oCurl );
		if (intval ($aStatus ["http_code"] ) == 200) {
			return array(
					'status' => true,
					'content' => $sContent,
					'code' => $aStatus ["http_code"],
			);
		} else {
			return array(
					'status' => false,
					'content' => json_encode(array("error" => $error, "url" => $url)),
					'code' => $aStatus ["http_code"],
			);
		}
	}

}
