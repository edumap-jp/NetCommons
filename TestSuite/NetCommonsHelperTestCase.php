<?php
/**
 * NetCommonsHelperTestCase class
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('View', 'View');
App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('CurrentControlPanel', 'NetCommons.Utility');

/**
 * NetCommonsHelperTestCase class
 *
 * @package NetCommons\NetCommons\TestSuite
 * @codeCoverageIgnore
 */
class NetCommonsHelperTestCase extends NetCommonsCakeTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = null;

/**
 * Helper name
 *
 * @var string
 */
	protected $_helperName = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		(new CurrentControlPanel())->setLanguage();

		if ($this->_helperName) {
			$this->loadHelper($this->_helperName);
		}
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		Current::$current = array();
		unset($this->_Controller);
		parent::tearDown();
	}

/**
 * Helperのロード処理
 *
 * @param string $helper ロードするHelper名(PluginName.HelperName)
 * @param array $viewVars $helper->_View->viewVarsに値をセットする配列
 * @param array $reqestData $helper->_View->request->dataに値をセットする配列
 * @return void
 */
	public function loadHelper($helper, $viewVars = array(), $reqestData = array()) {
		list($plugin, $helper) = pluginSplit($helper);
		if (! $plugin) {
			$plugin = $this->plugin;
		}

		$helperClass = $helper . 'Helper';
		App::uses($helperClass, Inflector::camelize($this->plugin) . '.View/Helper');
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$Controller = new Controller($CakeRequest, $CakeResponse);
		$Controller->set($viewVars);
		$Controller->request->data = $reqestData;

		$View = new View($Controller);
		$this->$helper = new $helperClass($View);
	}

}