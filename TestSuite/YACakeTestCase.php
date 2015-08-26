<?php
/**
 * YACakeTestCase file
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

CakeLog::drop('stdout');
CakeLog::drop('stderr');
App::uses('CakePlugin', 'Core');

/**
 * YACakeTestCase class
 *
 * @package NetCommons\TestSuite
 */
class YACakeTestCase extends CakeTestCase {

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = true;

/**
 * Fixtures
 *
 * @var array
 */
	protected $_fixtures = array(
		'plugin.blocks.block',
		'plugin.blocks.block_role_permission',
		'plugin.boxes.box',
		'plugin.boxes.boxes_page',
		'plugin.containers.container',
		'plugin.containers.containers_page',
		'plugin.frames.frame',
		'plugin.m17n.language',
		'plugin.pages.page',
		'plugin.plugin_manager.plugin',
		'plugin.roles.default_role_permission',
		'plugin.roles.role',
		'plugin.rooms.roles_room',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.room',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission',
		'plugin.users.user',
		//'plugin.users.users_language',
	);

/**
 * Fixtures load
 *
 * @param string $name The name parameter on PHPUnit_Framework_TestCase::__construct()
 * @param array  $data The date parameter on PHPUnit_Framework_TestCase::__construct()
 * @param string $dataName The dataName parameter on PHPUnit_Framework_TestCase::__construct()
 * @return void
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		if ($this->_isFixtureMerged) {
			$this->fixtures = array_merge($this->fixtures, $this->_fixtures);
		}
	}

/**
 * Load TestPlugin
 *
 * @param CakeTestCase $test CakeTestCase
 * @param string $plugin Plugin name
 * @param string $testPlugin Test plugin name
 * @return void
 */
	public static function loadTestPlugin(CakeTestCase $test, $plugin, $testPlugin) {
		$pluginPath = CakePlugin::path(Inflector::camelize($plugin));
		if (empty($pluginPath) || ! file_exists($pluginPath)) {
			$test->markTestAsSkipped(sprintf('Could not find %s in plugin paths', $pluginPath));
			return;
		}

		App::build(array(
			'Plugin' => array($pluginPath . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS)
		));
		CakePlugin::load($testPlugin);
	}
}
