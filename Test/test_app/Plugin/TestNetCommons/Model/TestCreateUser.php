<?php
/**
 * TestCreateAllUser Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * TestCreateAllUser Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\Test\test_app\TestNetCommons\Controller
 */
class TestCreateUser extends AppModel {

/**
 * Custom database table name
 *
 * @var string
 */
	public $useTable = 'create_users';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $hasOne = array(
		'TestCreateGroup' => array(
			'className' => 'TestNetCommons.TestCreateGroup',
			'foreignKey' => 'group_id',
		),
	);

}
