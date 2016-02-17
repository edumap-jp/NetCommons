<?php
/**
 * NetCommonsControllerTestCase
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerBaseTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');

/**
 * NetCommonsControllerTestCase
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\TestSuite
 * @codeCoverageIgnore
 */
class NetCommonsControllerTestCase extends NetCommonsControllerBaseTestCase {

/**
 * Load TestPlugin
 *
 * @param CakeTestCase $test CakeTestCase
 * @param string $plugin Plugin name
 * @param string $testPlugin Test plugin name
 * @return void
 */
	public static function loadTestPlugin(CakeTestCase $test, $plugin, $testPlugin) {
		NetCommonsCakeTestCase::loadTestPlugin($test, $plugin, $testPlugin);
	}

/**
 * Lets you do functional tests of a controller action.
 *
 * ### Options:
 *
 * - `data` Will be used as the request data. If the `method` is GET,
 *   data will be used a GET params. If the `method` is POST, it will be used
 *   as POST data. By setting `$options['data']` to a string, you can simulate XML or JSON
 *   payloads to your controllers allowing you to test REST webservices.
 * - `method` POST or GET. Defaults to POST.
 * - `return` Specify the return type you want. Choose from:
 *     - `vars` Get the set view variables.
 *     - `view` Get the rendered view, without a layout.
 *     - `contents` Get the rendered view including the layout.
 *     - `result` Get the return value of the controller action. Useful
 *       for testing requestAction methods.
 * - `type` json or html, Defaults to html.
 *
 * @param string $url The url to test
 * @param array $options See options
 * @return mixed
 */
	protected function _testAction($url = '', $options = []) {
		$options = array_merge(['type' => 'html'], $options);
		if ($options['type'] === 'json') {
			$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		}
		$ret = parent::_testAction($url, $options);
		return $ret;
	}

/**
 * Assert input tag
 *
 * ### $returnについて
 *  - viewFile: viewファイル名を戻す
 *  - json: JSONをでコードした配列を戻す
 *  - 上記以外: $this->testActionのreturnで指定した内容を戻す
 *
 * @param array $url URL配列
 * @param array $paramsOptions リクエストパラメータオプション
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return void
 */
	protected function _testNcAction($url = array(), $paramsOptions = array(), $exception = null, $return = 'view') {
		if ($exception && $return !== 'json') {
			$this->setExpectedException($exception);
		}

		//URL設定
		$params = array();
		if ($return === 'viewFile') {
			$params['return'] = 'view';
		} elseif ($return === 'json') {
			$params['return'] = 'view';
			$params['type'] = 'json';
			if ($exception === 'BadRequestException') {
				$status = 400;
			} elseif ($exception === 'ForbiddenException') {
				$status = 403;
			} else {
				$status = 200;
			}
		} else {
			$params['return'] = $return;
		}
		$params = Hash::merge($params, $paramsOptions);

		//テスト実施
		$view = $this->testAction(NetCommonsUrl::actionUrl($url), $params);
		if ($return === 'viewFile') {
			$result = $this->controller->view;
		} elseif ($return === 'json') {
			$result = json_decode($this->contents, true);
			$this->assertArrayHasKey('code', $result);
			$this->assertEquals($status, $result['code']);
		} else {
			$result = $view;
		}

		return $result;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return mixed テスト結果
 */
	protected function _testGetAction($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => 'get'), $exception, $return);

		if (! $exception && $assert) {
			if ($assert['method'] === 'assertActionLink') {
				$assert['url'] = Hash::merge($url, $assert['url']);
			}

			$this->asserts(array($assert), $result);
		}

		return $result;
	}

/**
 * addアクションのPOSTテスト
 *
 * @param array $method リクエストのmethod(post put delete)
 * @param array $data POSTデータ
 * @param array $urlOptions URLオプション
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return mixed テスト結果
 */
	protected function _testPostAction($method, $data, $urlOptions, $exception = null, $return = 'view') {
		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => $method, 'data' => $data), $exception, $return);

		return $result;
	}

/**
 * addアクションのValidateionErrorテスト
 *
 * @param array $method リクエストのmethod(post put delete)
 * @param array $data POSTデータ
 * @param array $urlOptions URLオプション
 * @param string|null $validationError ValidationError
 * @return mixed テスト結果
 */
	protected function _testActionOnValidationError($method, $data, $urlOptions, $validationError = null) {
		$data = Hash::remove($data, $validationError['field']);
		$data = Hash::insert($data, $validationError['field'], $validationError['value']);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$result = $this->_testNcAction($url, array('method' => $method, 'data' => $data));

		//バリデーションエラー
		$asserts = array(
			array('method' => 'assertNotEmpty', 'value' => $this->controller->validationErrors),
			array('method' => 'assertTextContains', 'expected' => $validationError['message']),
		);

		//チェック
		$this->asserts($asserts, $result);

		return $result;
	}

/**
 * ExceptionErrorのMockセット
 *
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @return void
 */
	protected function _mockForReturnFalse($mockModel, $mockMethod) {
		$this->_mockForReturn($mockModel, $mockMethod, false);
	}

/**
 * ExceptionErrorのMockセット
 *
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @return void
 */
	protected function _mockForReturnTrue($mockModel, $mockMethod) {
		$this->_mockForReturn($mockModel, $mockMethod, true);
	}

/**
 * ExceptionErrorのMockセット
 *
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @param bool $return 戻り値
 * @return void
 */
	protected function _mockForReturn($mockModel, $mockMethod, $return) {
		list($mockPlugin, $mockModel) = pluginSplit($mockModel);

		if (get_class($this->controller->$mockModel) === $mockModel) {
			$this->controller->$mockModel = $this->getMockForModel($mockPlugin . '.' . $mockModel, array($mockMethod));
		}
		$this->controller->$mockModel->expects($this->once())
			->method($mockMethod)
			->will($this->returnValue($return));
	}

/**
 * Generates a mocked controller and mocks any classes passed to `$mocks`. By
 * default, `_stop()` is stubbed as is sending the response headers, so to not
 * interfere with testing.
 *
 * ### Mocks:
 *
 * - `methods` Methods to mock on the controller. `_stop()` is mocked by default
 * - `models` Models to mock. Models are added to the ClassRegistry so any
 *   time they are instantiated the mock will be created. Pass as key value pairs
 *   with the value being specific methods on the model to mock. If `true` or
 *   no value is passed, the entire model will be mocked.
 * - `components` Components to mock. Components are only mocked on this controller
 *   and not within each other (i.e., components on components)
 *
 * @param string $controller Controller name
 * @param array $mocks List of classes and methods to mock
 * @return Controller Mocked controller
 */
	public function generateNc($controller, $mocks = array()) {
		list($plugin, $controller) = pluginSplit($controller);
		if (! $plugin) {
			$plugin = Inflector::camelize($this->plugin);
		}

		$this->generate($plugin . '.' . $controller, Hash::merge(array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
			)
		), $mocks));
	}

/**
 * Asserts
 *
 * @param array $asserts テストAssert
 * @param string $result Result data
 * @return void
 */
	public function asserts($asserts, $result) {
		//チェック
		if (isset($asserts)) {
			foreach ($asserts as $assert) {
				$assertMethod = $assert['method'];

				if ($assertMethod === 'assertInput') {
					$this->$assertMethod($assert['type'], $assert['name'], $assert['value'], $result);
					continue;
				}

				if ($assertMethod === 'assertActionLink') {
					$this->$assertMethod($assert['action'], $assert['url'], $assert['linkExist'], $result);
					continue;
				}

				if (! isset($assert['value'])) {
					$assert['value'] = $result;
				}
				if (isset($assert['expected'])) {
					$this->$assertMethod($assert['expected'], $assert['value']);
				} else {
					$this->$assertMethod($assert['value']);
				}
			}
		}
	}

/**
 * Assert input tag
 *
 * @param string $tagType タグタイプ(input or textearea or button)
 * @param string $name inputタグのname属性
 * @param string $value inputタグのvalue値
 * @param string $result Result data
 * @param string $message メッセージ
 * @return void
 */
	public function assertInput($tagType, $name, $value, $result, $message = null) {
		$result = str_replace("\n", '', $result);

		if ($name) {
			$patternName = '.*?name="' . preg_quote($name, '/') . '"';
		} else {
			$patternName = '';
		}

		if (! $value) {
			$patternValue = '';
		} elseif (in_array($value, ['checked', 'selected'], true)) {
			$patternValue = '.*?' . $value . '="' . $value . '"';
		} else {
			$patternValue = '.*?value="' . $value . '"';
		}

		if ($tagType === 'textarea') {
			$this->assertRegExp(
				'/<textarea' . $patternName . '.*?>.*?<\/textarea>/', $result, $message
			);
		} elseif ($tagType === 'option') {
			$this->assertRegExp(
				'/<option.*?value="' . preg_quote($name, '/') . '"' . $patternValue . '.*?>/', $result, $message
			);
		} elseif ($tagType === 'form') {
			$this->assertRegExp(
				'/<form.*?action=".*?' . preg_quote($value, '/') . '.*"' . $patternName . '.*?>/', $result, $message
			);
		} elseif (in_array($tagType, ['input', 'select', 'button'], true)) {
			$this->assertRegExp(
				'/<' . $tagType . $patternName . $patternValue . '.*?>/', $result, $message
			);
		}
	}

/**
 * Assert アクションリンク
 *
 * @param string $action アクション
 * @param array $urlOptions URLオプション
 * @param bool $linkExist リンクの有無
 * @param string $result Result data
 * @param string $message メッセージ
 * @return void
 */
	public function assertActionLink($action, $urlOptions, $linkExist, $result, $message = null) {
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);

		$url['action'] = $action;

		if (isset($url['frame_id']) && ! Current::read('Frame.id')) {
			unset($url['frame_id']);
		}
		if (isset($url['block_id']) && ! Current::read('Block.id')) {
			unset($url['block_id']);
		}

		if ($linkExist) {
			$method = 'assertRegExp';
		} else {
			$method = 'assertNotRegExp';
		}
		$expected = '/' . preg_quote(NetCommonsUrl::actionUrl($url), '/') . '/';

		//チェック
		$this->$method($expected, $result, $message);
	}

}
