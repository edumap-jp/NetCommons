<?php
/**
 * NetCommonsの機能に必要な情報を取得する内容をまとめたUtility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CurrentGetSystem', 'NetCommons.Utility');

/**
 * NetCommonsの機能に必要な情報(ルーム関連)を取得する内容をまとめたUtility
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\Utility
 */
class CurrentGetRoom extends CurrentGetAppObject {

/**
 * CurrentGetRoomインスタンス
 *
 * @var CurrentGetRoom
 */
	private static $___instance;

/**
 * ログインなしで参照できるスペースリストデータ
 *
 * @var array
 */
	public static $__spacesWOLogin = [
		Space::PUBLIC_SPACE_ID,
	];

/**
 * クラス内で処理するコントローラを保持
 *
 * @var Controller
 */
	private $_controller;

/**
 * Roomモデル
 *
 * @var Room
 */
	public $Room;

/**
 * Roomモデル
 *
 * @var Space
 */
	public $Space;

/**
 * RolesRoomモデル
 *
 * @var RolesRoom
 */
	public $RolesRoom;

/**
 * RoomRolePermissionモデル
 *
 * @var RoomRolePermission
 */
	public $RoomRolePermission;

/**
 * RolesRoomsUserモデル
 *
 * @var RolesRoomsUser
 */
	public $RolesRoomsUser;

/**
 * RolesRoomsUserモデル
 *
 * @var RolesRoomsUser
 */
	public $RoomsLanguage;

/**
	* 一度取得したルームデータを保持
 *
 * @var array|null
 */
	private $__room = null;

/**
	* 一度取得したスペースデータを保持
 *
 * @var array|null
 */
	private $__space = null;

/**
 * 一度取得した参加ルーム(roles_rooms_uses)のIDリストを保持
 *
 * ※カレンダーなどで使用できるように取得する
 *
 * @var array|null
 */
	private $__memberRoomIds = null;

/**
 * 一度取得したルーム権限(roles_rooms)データを保持
 *
 * @var array|null
 */
	private $__roleRooms = null;

/**
 * ルーム権限IDからルームIDに変換するためのIDを保存
 *
 * @var array|null
 */
	private $__roomIdById = null;

/**
 * 一度取得したルームに対するプラグインデータを保持
 *
 * @var array|null
 */
	private $__plugins = null;

/**
 * ログインしたユーザIDを保持
 *
 * @var string ユーザID(intの文字列)
 */
	private $__userId = null;

/**
 * 表示している言語IDを保持
 *
 * @var string 言語ID(intの文字列)
 */
	private $__langId = null;

/**
 * コンストラクター
 *
 * @param Controller $controller コントローラ
 * @return void
 */
	public function __construct(Controller $controller) {
		$this->_controller = $controller;

		$this->Space = ClassRegistry::init('Rooms.Space');
		$this->Room = ClassRegistry::init('Rooms.Room');
		$this->RolesRoomsUser = ClassRegistry::init('Rooms.RolesRoomsUser');
		$this->RolesRoom = ClassRegistry::init('Rooms.RolesRoom');
		$this->RoomRolePermission = ClassRegistry::init('Rooms.RoomRolePermission');
		$this->PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');
		$this->Plugin = ClassRegistry::init('PluginManager.Plugin');

		$this->__userId = Current::read('User.id');
		$this->__langId = Current::read('Language.id');
	}

/**
 * インスタンスの取得
 *
 * @param Controller $controller コントローラ
 * @return CurrentGetRoom
 */
	public static function getInstance(Controller $controller) {
		return parent::_getInstance($controller, __CLASS__);
	}

/**
 * ログインなしで閲覧可能なスペースIDリストに追加する
 *
 * @param string $spaceId スペースID(intの文字列)
 * @return void
 */
	public static function addSpaceIdsWithoutLogin($spaceId) {
		if (! in_array($spaceId, self::$__spacesWOLogin, true)) {
			self::$__spacesWOLogin[] = $spaceId;
		}
	}

/**
 * ルームデータを取得する
 *
 * @param string $roomId ルームID(intの文字列)
 * @return array
 */
	public function findRoom($roomId) {
		if ($this->__room) {
			return $this->__room;
		}

		$room = $this->Room->find('first', array(
			'recursive' => -1,
			'fields' => [
				$this->Room->alias . '.id',
				$this->Room->alias . '.space_id',
				$this->Room->alias . '.page_id_top',
				$this->Room->alias . '.parent_id',
				//$this->Room->alias . '.lft',
				//$this->Room->alias . '.rght',
				$this->Room->alias . '.active',
				//$this->Room->alias . '.in_draft',
				$this->Room->alias . '.default_role_key',
				$this->Room->alias . '.need_approval',
				$this->Room->alias . '.default_participation',
				$this->Room->alias . '.page_layout_permitted',
				$this->Room->alias . '.theme',
			],
			'conditions' => [
				'id' => $roomId
			],

		));

		$this->__room = $room;
		$this->__space = $this->Space->getSpace($room['Room']['space_id']);

		return $this->__room;
	}

/**
 * プライベートルームデータを取得する
 *
 * @param string $userId ユーザID(intの文字列)
 * @return array
 */
	public function findPrivateRoom($userId) {
		if ($this->__room) {
			return $this->__room;
		}

		$room = $this->Room->getPrivateRoomByUserId($userId);
		$this->__room = $room;
		$this->__space = $this->Space->getSpace($room['Room']['space_id']);

		return $this->__room;
	}

/**
 * 参加ルーム(roles_rooms_uses)のIDリストを取得する
 * ※同時に、ルーム権限データも取得する
 *
 * @param string $userId ユーザID(intの文字列)
 * @return array
 */
	public function getMemberRoomIds() {
		if (isset($this->__memberRoomIds)) {
			return $this->__memberRoomIds;
		}

		if ($this->__userId) {
			$rolesRoomsUser = $this->RolesRoomsUser->find('all', [
				'recursive' => -1,
				'fields' => [
					$this->RolesRoom->alias . '.id',
					$this->RolesRoom->alias . '.room_id',
					$this->RolesRoom->alias . '.role_key',
				],
				'conditions' => [
					'RolesRoomsUser.user_id' => $this->__userId
				],
				'joins' => [
					[
						'table' => $this->Room->table,
						'alias' => $this->Room->alias,
						'type' => 'INNER',
						'conditions' => [
							$this->RolesRoomsUser->alias . '.room_id' . ' = ' . $this->Room->alias . ' .id',
						],
					],
					[
						'table' => $this->RolesRoom->table,
						'alias' => $this->RolesRoom->alias,
						'type' => 'INNER',
						'conditions' => [
							$this->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $this->RolesRoom->alias . ' .id',
						],
					],
				],
			]);
			$this->__memberRoomIds = [];
			foreach ($rolesRoomsUser as $roleRoom) {
				$roomId = $roleRoom['RolesRoom']['room_id'];
				$roleRoomId = $roleRoom['RolesRoom']['id'];
				$this->__memberRoomIds[] = $roomId;
				$this->__roleRooms[$roomId] = $roleRoom;
				$this->__roomIdById[$roleRoomId] = $roomId;
			}
		} else {
			$this->__memberRoomIds = [];
			$this->__roleRooms = [];
			$this->__roomIdById = [];
		}
		return $this->__memberRoomIds;
	}

/**
 * ルームIDからルーム権限データ取得
 *
 * @return array
 */
	public function findRoleRoomByRoomId($roomId = null) {
		if (! isset($this->__roleRooms)) {
			$roomIds = $this->getMemberRoomIds();
			if (! $roomIds) {
				return null;
			}
		}
		if (isset($this->__roleRooms[$roomId])) {
			return $this->__roleRooms[$roomId];
		} else {
			return null;
		}
	}

/**
 * ルーム権限IDからルーム権限データ取得
 *
 * @return array
 */
	public function findRoleRoomById($roleRoomId = null) {
		if (! isset($this->__roomIdById)) {
			$roomIds = $this->getMemberRoomIds();
			if (! $roomIds) {
				return [];
			}
		}
		if (isset($this->__roomIdById[$roleRoomId])) {
			$roomId = $this->__roomIdById[$roleRoomId];
			return $this->__roleRooms[$roomId];
		} else {
			return [];
		}
	}

/**
 * ルーム権限IDリスト取得
 *
 * @return array
 */
	public function findRoleRoomIds() {
		if (! isset($this->__roomIdById)) {
			$roomIds = $this->getMemberRoomIds();
			if (! $roomIds) {
				return [];
			}
		}
		return array_keys($this->__roomIdById);
	}

/**
 * ルームプラグインデータ取得
 *
 * @return array
 */
	public function findPluginsRoom($roomId) {
		if (isset($this->__plugins[$roomId])) {
			return $this->__plugins[$roomId];
		}

		$pluginsRoom = $this->PluginsRoom->find('all', [
			'recursive' => -1,
			'fields' => [
				//$this->PluginsRoom->alias . '.id',
				//$this->PluginsRoom->alias . '.room_id',
				$this->PluginsRoom->alias . '.plugin_key',
			],
			'conditions' => [
				'room_id' => $roomId
			],
		]);

		$pluginKeys = [];
		foreach ($pluginsRoom as $pluginRoom) {
			$pluginKeys[] = $pluginRoom['PluginsRoom']['plugin_key'];
		}

		$instance = CurrentGetSystem::getInstance($this->_controller);
		$this->__plugins[$roomId] = $instance->findPlugins($pluginKeys, $this->__langId);

		return $this->__plugins[$roomId];
	}

}
