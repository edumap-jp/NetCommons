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
 * @param array $requestBody Body/クエリ
 *
 * @throws RuntimeException 通信エラー
 *
 * @return array
 */
   static public function post($path, $requestBody) {
		$url = SiteSettingUtil::read('DataServer.data_server_url', 'http://data:3000') . $path;

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		if (isset($requestBody)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestBody));
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		curl_close($curl);
		if ($errno !== CURLE_OK) {
			throw new RuntimeException($error, $errno);
		}
		if ($code !== 200) {
			throw new RuntimeException($code . ': ' . $result);
		}
		return json_decode($result, true);
    }
}
