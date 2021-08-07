<?php
/**
 * Migration file
 *
 * @author Wataru Nishimoto <watura@willbooster.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
App::uses('PluginBehavior', 'PluginManager.Model/Behavior');

/**
 * システム管理用データ - 担当者への連絡通知
 *
 * @package NetCommons\SiteManager\Config\Migration
 */
class AddRecordDataServerUrl extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_record_data_server_url';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * recodes
 *
 * @var array $migration
 */
	public $records = array(
		'SiteSetting' => array(
			// ** データ管理サーバーのURL
			array(
				'language_id' => '0',
				'key' => 'DataServer.data_server_url',
				//検証環境や動作確認環境では、
				//rundeckの/opt/scripts/app_docker/setup_site_nc3_json.shに設定変更スクリプト追加している
				'value' => 'https://data-mng.edumap.jp',
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
		if (! PluginBehavior::staticRunMigration('PluginManager', $this->connection)) {
			return false;
		}
		if (! PluginBehavior::staticRunMigration('SiteManager', $this->connection)) {
			return false;
		}
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'down') {
			return true;
		}

		foreach ($this->records as $model => $records) {
			if (!$this->updateRecords($model, $records)) {
				return false;
			}
		}
		return true;
	}
}
