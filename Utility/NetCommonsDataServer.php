<?php
/**
 * DataSeverアクセス Utility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @author Wataru Nishimoto <watura@willbooster.com>
 * @author Kazunori Sakamoto <exkazuu@willbooster.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * DataSeverアクセス Utility
 *
 * @package NetCommons\NetCommons\Utility
 */
class NetCommonsDataServer {

/**
 * データサーバにPostリクエストを送信しレスポンスをJSONで取得する。
 *
 * @param string $path パス
 * @param array $requestBody Body
 *
 * @throws RuntimeException 通信エラー
 *
 * @return array
 */
	static public function post($path, $requestBody = null) {
		$url = Self::__url() . $path;

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		if (isset($requestBody)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestBody));
		}
		$result = Self::__request($curl);
		return json_decode($result, true);
	}

/**
 * データサーバにGetリクエストを送信しレスポンスをJSONで取得する。
 *
 * @param string $path パス
 * @param array $requestQuery クエリ
 *
 * @throws RuntimeException 通信エラー
 *
 * @return array
 */
	static public function get($path, $requestQuery = null) {
		$url = Self::__url() . $path;
		if (isset($requestQuery)) {
			$data = http_build_query($requestQuery);
			if (strpos($path, '?') !== false) {
				$url = $url . '&' . $data;
			} else {
				$url = $url . '?' . $data;
			}
		}

		$curl = curl_init($url);
		$result = Self::__request($curl);
		return json_decode($result, true);
	}

/**
 * データサーバのURL
 *
 * @return string
 */
	static private function __url() {
		return SiteSettingUtil::read('DataServer.data_server_url', 'https://data-mng.edumap.jp');
	}

/**
 * データサーバにリクエストを送信する
 *
 * @param curl $curl curl
 *
 * @throws RuntimeException 通信エラー
 *
 * @return array
 */
	static private function __request($curl) {
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		curl_close($curl);
		if ($errno !== CURLE_OK) {
			throw new RuntimeException($error, $errno);
		}
		if ($code !== 200 && $code !== 202) {
			throw new RuntimeException($code . ': ' . $result);
		}
		return $result;
	}
}
