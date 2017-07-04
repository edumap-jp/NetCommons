<?php
/**
 * SetBlockRolePermissionsTestBase
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Current', 'NetCommons.Utility');
App::uses('CurrentFrame', 'NetCommons.Utility');
App::uses('BlockSettingBehavior', 'Blocks.Model/Behavior');

/**
 * SetBlockRolePermissionsTestBase
 *
 */
class SetBlockRolePermissionsTestBase extends CakeTestCase {

/**
 * CurrentFrame
 *
 * @var CurrentFrame
 */
	public $CurrentFrame = null;

/**
 * MockBlockRolePermission
 *
 * @var PHPUnit_Framework_MockObject_MockObject
 */
	public $MockBlockRPermission = null;

/**
 * MockBlockSetting
 *
 * @var PHPUnit_Framework_MockObject_MockObject
 */
	public $MockBlockSetting = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CurrentFrame = new CurrentFrame();
		$this->MockBlockRPermission = $this->getMockForModel('Blocks.BlockRolePermission', ['find']);
		$this->MockBlockSetting = $this->getMockForModel('Blocks.BlockSetting', ['find']);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		Current::$current = [];
		unset(
			$this->CurrentFrame,
			$this->MockBlockRPermission,
			$this->MockBlockSetting
		);
	}

/**
 * Get Room test data
 *
 * @return array
 */
	protected function _getRoomTestData() {
		return [
			'Room approval is required' => [
				'Room' => [
					'need_approval' => '1'
				]
			],
			'Room approval is not required' => [
				'Room' => [
						'need_approval' => '0'
				]
			],
		];
	}

/**
 * Get Block test data
 *
 * @return array
 */
	protected function _getBlockTestData() {
		return [
			'Block exists' => [
				'Block' => [
					'key' => 'dummy'
				]
			],
			'Block not exists' => [
				'Block' => [
				]
			],
		];
	}

/**
 * Get Block test data
 *
 * @return array
 */
	protected function _getBlockSettingTestData() {
		return [
			'BlockSetting use both' => [
				'BlockSetting' => [
					BlockSettingBehavior::FIELD_USE_WORKFLOW => '1',
					BlockSettingBehavior::FIELD_USE_COMMENT_APPROVAL => '1',
				],
			],
			'BlockSetting use comment only' => [
				'BlockSetting' => [
					BlockSettingBehavior::FIELD_USE_WORKFLOW => '0',
					BlockSettingBehavior::FIELD_USE_COMMENT_APPROVAL => '1',
				],
			],
			'BlockSetting unuse both' => [
				'BlockSetting' => [
					BlockSettingBehavior::FIELD_USE_WORKFLOW => '0',
					BlockSettingBehavior::FIELD_USE_COMMENT_APPROVAL => '0',
				],
			],
			'BlockSetting use content and comment is null' => [
				'BlockSetting' => [
					BlockSettingBehavior::FIELD_USE_WORKFLOW => '1',
				],
			],
			'BlockSetting unuse content and comment is null' => [
				'BlockSetting' => [
					BlockSettingBehavior::FIELD_USE_WORKFLOW => '0',
				],
			],
			'BlockSetting both are null' => [
				'BlockSetting' => [
				],
			],
		];
	}

/**
 * Get RolePermission test data for room_administrator
 *
 * @return array
 */
	protected function _getRolePermissionTestDataForRoomAdministrator() {
		return [
			'room_administrator' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				]
			]
		];
	}

/**
 * Get RolePermission test data for chief_editor
 *
 * @return array
 */
	protected function _getRolePermissionTestDataForChiefEditor() {
		return [
			'chief_editor content true' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				]
			],
			'chief_editor content false' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				]
			],
		];
	}

/**
 * Get RolePermission test data for editor
 *
 * @return array
 */
	protected function _getRolePermissionTestDataForEditor() {
		return $this->__getRolePermissionTestDataForEditorOfCommentNotNullContentTrue() +
		$this->__getRolePermissionTestDataForEditorOfCommentNotNullContentFalse() +
			$this->__getRolePermissionTestDataForEditorOfCommentNull();
	}

/**
 * Get RolePermission test data for editor of comment not null content true
 *
 * @return array
 */
	private function __getRolePermissionTestDataForEditorOfCommentNotNullContentTrue() {
		return [
			'editor both true' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'BlockRolePermission' => [
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				]
			],
			'editor content true only' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'BlockRolePermission' => [
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				]
			],
		];
	}

/**
 * Get RolePermission test data for editor of comment not null content false
 *
 * @return array
 */
	private function __getRolePermissionTestDataForEditorOfCommentNotNullContentFalse() {
		return [
			'editor comment true only' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'BlockRolePermission' => [
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => true,
					],
				]
			],
			'editor both false' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'BlockRolePermission' => [
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				]
			]
		];
	}

/**
 * Get RolePermission test data for editor of comment null
 *
 * @return array
 */
	private function __getRolePermissionTestDataForEditorOfCommentNull() {
		return [
			'editor content true and comment is null' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => true,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
			],
			'editor content false and comment is null' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
			],
		];
	}

/**
 * Get RolePermission test data for general_user
 *
 * @return array
 */
	protected function _getRolePermissionTestDataForGeneralUser() {
		return [
			'general_user' => [
				'DefaultRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
				'RoomRolePermission' => [
					'content_publishable' => [
						'permission' => 'content_publishable',
						'value' => false,
					],
					'content_comment_publishable' => [
						'permission' => 'content_comment_publishable',
						'value' => false,
					],
				],
			],

		];
	}

/**
 * Get expected data
 *
 * @return array
 */
	protected function _getExpectedData() {
		return [
			'both true' => [
				'content_publishable' => [
					'permission' => 'content_publishable',
					'value' => true,
				],
				'content_comment_publishable' => [
					'permission' => 'content_comment_publishable',
					'value' => true,
				],
			],
			'content true only' => [
				'content_publishable' => [
					'permission' => 'content_publishable',
					'value' => true,
				],
				'content_comment_publishable' => [
					'permission' => 'content_comment_publishable',
					'value' => false,
				],
			],
			'comment true only' => [
				'content_publishable' => [
					'permission' => 'content_publishable',
					'value' => false,
				],
				'content_comment_publishable' => [
					'permission' => 'content_comment_publishable',
					'value' => true,
				],
			],
			'both false' => [
				'content_publishable' => [
					'permission' => 'content_publishable',
					'value' => false,
				],
				'content_comment_publishable' => [
					'permission' => 'content_comment_publishable',
					'value' => false,
				],
			],
		];
	}

/**
 * Set MockBlockSetting
 *
 * @param array $data test data
 * @return void
 */
	protected function _setMockBlockSetting($data) {
		if (Current::read('Room.need_approval') ||
			!isset(Current::$current['Block']['key'])
		) {
			$this->MockBlockSetting
				->expects($this->never())
				->method('find');

			return;
		}

		$this->MockBlockSetting
			->expects($this->once())
			->method('find')
			->with(
				'list',
				[
					'recursive' => -1,
					'fields' => ['field_name', 'value'],
					'conditions' => [
						'block_key' => Current::$current['Block']['key'],
						'field_name' => [
							BlockSettingBehavior::FIELD_USE_WORKFLOW,
							BlockSettingBehavior::FIELD_USE_COMMENT_APPROVAL
						],
					],
				]
			)
			->will($this->returnValue($data['BlockSetting']));
	}

/**
 * Set MockBlockRolePermission
 *
 * @param array $data test data
 * @return void
 */
	protected function _setMockBlockRolePermission($data) {
		unset(Current::$current['BlockRolePermission']);

		if (!isset(Current::$current['Block']['key'])) {
			$this->MockBlockRPermission
				->expects($this->never())
				->method('find');

			return;
		}

		Current::$current['RolesRoom']['id'] = 'dummy';
		$blockRolePermissions = Hash::get($data, ['BlockRolePermission'], []);
		$value = [];
		foreach ($blockRolePermissions as $permission => $blockRolePermission) {
			$value[] = [
				'BlockRolePermission' => [
					'permission' =>	$permission,
					'value' => $blockRolePermission['value'],
				]
			];
		}
		$this->MockBlockRPermission
			->expects($this->once())
			->method('find')
			->with(
				'all',
				[
					'recursive' => -1,
					'conditions' => [
						'roles_room_id' => Current::$current['RolesRoom']['id'],
						'block_key' => Current::$current['Block']['key'],
					],
				]
			)
			->will($this->returnValue($value));
	}

}
