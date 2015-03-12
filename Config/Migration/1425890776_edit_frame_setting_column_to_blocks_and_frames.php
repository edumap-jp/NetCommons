<?php
class EditFrameSettingColumnToBlocksAndFrames extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'edit_frame_setting_column_to_blocks_and_frames';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'blocks' => array(
					'plugin_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'block key |  プラグインKEY | plugins.key | ', 'charset' => 'utf8', 'after' => 'room_id'),
					'public_type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'comment' => '一般以下のパートが閲覧可能かどうか。
（0:非公開, 1:公開, 2:期間限定公開）

ルーム配下ならルーム管理者、またはそれに準ずるユーザ(room_parts.edit_page, room_parts.create_page 双方が true のユーザ)はこの値に関わらず閲覧できる。
e.g.) ルーム管理者、またはそれに準ずるユーザ: ルーム管理者、編集長

期間限定公開の場合、現在時刻がfrom-toカラムの範囲内の時に公開。', 'after' => 'name'),
					'from' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Datetime display frame from.', 'after' => 'public_type'),
					'to' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Datetime display frame to.', 'after' => 'from'),
				),
				'frames' => array(
					'header_type' => array('type' => 'string', 'null' => false, 'default' => 'default', 'collate' => 'utf8_general_ci', 'comment' => 'Header type of the frame.', 'charset' => 'utf8', 'after' => 'name'),
				),
			),
			'drop_field' => array(
				'frames' => array('is_published', 'from', 'to'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'blocks' => array('plugin_key', 'public_type', 'from', 'to'),
				'frames' => array('header_type'),
			),
			'create_field' => array(
				'frames' => array(
					'is_published' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '一般以下のパートが閲覧可能かどうか。

ルーム配下ならルーム管理者、またはそれに準ずるユーザ(room_parts.edit_page, room_parts.create_page 双方が true のユーザ)はこの値に関わらず閲覧できる。
e.g.) ルーム管理者、またはそれに準ずるユーザ: ルーム管理者、編集長'),
					'from' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Datetime display frame from.'),
					'to' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Datetime display frame to.'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}