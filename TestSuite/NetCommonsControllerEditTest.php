<?php
/**
 * NetCommonsControllerEditTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * NetCommonsControllerEditTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\TestSuite
 */
class NetCommonsControllerEditTest extends NetCommonsControllerTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->generateNc(Inflector::camelize($this->_controller));
	}

/**
 * editアクションのGETテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return array テスト結果
 */
	protected function _testEditGet($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => 'get'), $exception, $return);

		if (! $exception && $assert) {
			$this->asserts(array($assert), $result);
		}

		return $result;
	}

/**
 * editアクションのPOSTテスト
 *
 * @param array $data POSTデータ
 * @param array $urlOptions URLオプション
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return array テスト結果
 */
	protected function _testEditPost($data, $urlOptions, $exception = null, $return = 'view') {
		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => 'put', 'data' => $data), $exception, $return);

		return $result;
	}

/**
 * editアクションのValidateionErrorテスト
 *
 * @param array $data POSTデータ
 * @param string $role ロール
 * @param array $urlOptions URLオプション
 * @param string|null $validationError ValidationError
 * @return array テスト結果
 */
	protected function _testEditValidationError($data, $urlOptions, $validationError = null) {
		$data = Hash::remove($data, $validationError['field']);
		$data = Hash::insert($data, $validationError['field'], $validationError['value']);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => 'put', 'data' => $data));

		//バリデーションエラー
		$asserts = array(
			array('method' => 'assertNotEmpty', 'value' => $this->controller->validationErrors),
			array('method' => 'assertTextContains', 'expected' => $validationError['message']),
		);

		//チェック
		$this->asserts($asserts, $result);

		return $result;
	}

}
