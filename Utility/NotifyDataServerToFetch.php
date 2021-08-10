<?php
/**
 * DataSeverにプッシュ通知するUtility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsDataServer', 'NetCommons.Utility');
App::uses('NetCommonsCache', 'NetCommons.Utility');
App::uses('NetCommonsCDNCache', 'NetCommons.Utility');
App::uses('Space', 'Rooms.Model');

/**
 * DataSeverにプッシュ通知するUtility
 *
 * @package NetCommons\NetCommons\Utility
 */
class NotifyDataServerToFetch {

/**
 * 通知するか否か
 *
 * @var bool
 */
	protected static $_notify = false;

/**
 * NetCommonsCache
 *
 * @var NetCommonsCache
 */
	protected static $_ncCache;

/**
 * notifyメソッドでDataSeverにプッシュ通知するために変数セットする
 *
 * @return void
 */
	public static function set() {
		self::$_notify = true;
	}

/**
 * notifyメソッドでDataSeverにプッシュ通知するために変数セットする
 *
 * @return void
 */
	public static function clear() {
		self::$_notify = false;
	}

/**
 * notifyメソッドでDataSeverにプッシュ通知するか否か
 *
 * @return bool
 */
	public static function isset() {
		return self::$_notify;
	}

/**
 * DataSeverにプッシュ通知する
 *
 * @return void
 */
	public static function notify() {
		/** @var SchoolInformation $SchoolInformation */
		$SchoolInformation = ClassRegistry::init('SchoolInformations.SchoolInformation');

		$edmKey = $SchoolInformation->getSchoolInformation()['SchoolInformation']['edumap_key'];
		if (empty($edmKey)) {
			return;
		}
		$query = [
			'hostname' => $edmKey,
		];
		try {
			NetCommonsDataServer::get('/jobs/sync_immediately', $query);
		}
		catch (exception $e) {}

		$notifyTime = self::__getDbNotifyTime();
		self::__writeCacheNotifyTime($notifyTime);

		self::clear();
	}

/**
 * DBから通知時間を取得
 *
 * 時限式で通知するモノがない場合は、0を返す
 *
 * @return int unix timestampを返す
 */
	private static function __getDbNotifyTime() {
		$now = (new DateTime('now', (new DateTimeZone('UTC'))))->format('Y-m-d H:i:s');

		/** @var Room $Room */
		$Room = ClassRegistry::init('Rooms.Room');
		$roomIds = self::__getPublicRoomIds($Room);
		if (empty($roomIds)) {
			return 0;
		}

		/** @var Block $Block */
		$Block = ClassRegistry::init('Blocks.Block');
		$blockPublishTime = self::__getDbPublishTime($Block, $now, $roomIds);

		/** @var Topic $Topic */
		$Topic = ClassRegistry::init('Topics.Topic');
		$topicPublishTime = self::__getDbPublishTime($Topic, $now, $roomIds);

		return self::__compareNotifyTime($blockPublishTime, $topicPublishTime);
	}

/**
 * blocksテーブル、topicsテーブルの公開日時から次回通知する時間を比較して返す
 *
 * 時限式で通知するモノがない場合は、0を返す
 *
 * @param string|null $blockPublishTime
 * @param string|null $topicPublishTime
 *
 * @return int unix timestampを返す
 */
	private static function __compareNotifyTime($blockPublishTime, $topicPublishTime) {
		$utcTimezone = new DateTimeZone('UTC');

		if (is_string($blockPublishTime)) {
			$blockPublishTime = (new DateTime($blockPublishTime, $utcTimezone))->getTimestamp();
		} else {
			$blockPublishTime = 0;
		}

		if (is_string($topicPublishTime)) {
			$topicPublishTime = (new DateTime($topicPublishTime, $utcTimezone))->getTimestamp();
		} else {
			$topicPublishTime = 0;
		}

		if ($blockPublishTime && $topicPublishTime) {
			if ($blockPublishTime > $topicPublishTime) {
				$notifyTime = $topicPublishTime;
			} else {
				$notifyTime = $blockPublishTime;
			}
		} elseif ($blockPublishTime) {
			$notifyTime = $blockPublishTime;
		} elseif ($topicPublishTime) {
			$notifyTime = $topicPublishTime;
		} else {
			$notifyTime = 0;
		}

		return $notifyTime;
	}

/**
 * パブリックスペース内にあるルームIDのリストを返す
 *
 * @param Model $model 実行するModel
 *
 * @return array
 */
	private static function __getPublicRoomIds(Model $model) {
		$table = $model->tablePrefix . $model->table;
		$alias = $model->alias;

		$sql = 'SELECT' .
				' ' . $alias . '.id ' .
				'FROM ' . $table . ' ' . $alias . ' ' .
				'WHERE ' . $alias . '.space_id = \'' . \Space::PUBLIC_SPACE_ID . '\'';
		$rooms = $model->query($sql);

		$roomIds = [];
		foreach ($rooms as $room) {
			$roomIds[] = $room[$alias]['id'];
		}

		return $roomIds;
	}

/**
 * DBから現在から直近の公開時刻を取得
 *
 * 戻り値は、公開開始時刻と公開終了時刻の小さい方を返す
 *
 * @param Model $model 実行するModel
 * @param string $now 現在時刻(Y-m-d H:i:s)
 * @param array $roomIds ルームIDリスト
 *
 * @return string |null
 */
	private static function __getDbPublishTime(Model $model, $now, $roomIds) {
		$table = $model->tablePrefix . $model->table;
		$alias = $model->alias;

		$virtualFields = $model->virtualFields;
		$model->virtualFields['min_publish_start'] = '';
		$model->virtualFields['min_publish_end'] = '';

		$sql = 'SELECT' .
				' MIN(' . $alias . '.publish_start) AS ' . $alias . '__min_publish_start ' .
				'FROM ' . $table . ' ' . $alias . ' ' .
				'WHERE ' . $alias . '.room_id IN (\'' . implode('\',\'', $roomIds) . '\') ' .
				'AND ' . $alias . '.publish_start > \'' . $now . '\'';

		$publish = $model->query($sql);
		$publishStart = $publish[0][$alias]['min_publish_start'] ?? null;

		$sql = 'SELECT' .
				' MIN(' . $alias . '.publish_end) AS ' . $alias . '__min_publish_end ' .
				'FROM ' . $table . ' ' . $alias . ' ' .
				'WHERE ' . $alias . '.room_id IN (\'' . implode('\',\'', $roomIds) . '\') ' .
				'AND ' . $alias . '.publish_end > \'' . $now . '\'';
		$publish = $model->query($sql);
		$publishEnd = $publish[0][$alias]['min_publish_end'] ?? null;

		$model->virtualFields = $virtualFields;

		if ($publishStart && $publishEnd) {
			if ($publishStart > $publishEnd) {
				$publishTime = $publishEnd;
			} else {
				$publishTime = $publishStart;
			}
		} elseif ($publishStart) {
			$publishTime = $publishStart;
		} elseif ($publishEnd) {
			$publishTime = $publishEnd;
		} else {
			$publishTime = null;
		}

		return $publishTime;
	}

/**
 * NetCommonsCacheオブジェクトを生成する
 *
 * @return NetCommonsCache
 */
	private static function __createCacheObject() {
		if (! self::$_ncCache) {
			self::$_ncCache =
					new NetCommonsCache('notify_data_server_to_fetch', false, 'netcommons_core');
		}
		return self::$_ncCache;
	}

/**
 * キャッシュファイルから通知時間リストを返す
 *
 * @return string
 */
	private static function __readCacheNotifyTime() {
		$ncCache = self::__createCacheObject();

		$notifyTime = self::__createCacheObject()->read('notifiy_times');
		if (is_null($notifyTime)) {
			$notifyTime = self::__getDbNotifyTime();
			self::__writeCacheNotifyTime($notifyTime);
		}

		return $notifyTime;
	}

/**
 * キャッシュファイルに通知時間を書き込む
 *
 * @param string $notifyTime
 *
 * @return void
 */
	private static function __writeCacheNotifyTime($notifyTime) {
		self::__createCacheObject()->write($notifyTime, 'notifiy_times');
	}

/**
 * 時間指定で通知するかどうか
 *
 * @return bool
 */
	public static function isTimedNotify() {
		$notifyTime = self::__readCacheNotifyTime();
		$nowTimestamp = (new DateTime('now', (new DateTimeZone('UTC'))))->getTimestamp();
		if ($notifyTime > 0 &&
				$notifyTime < $nowTimestamp) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 時間通りになったため通知する
 *
 * 時間通りになったため通知するためには、CDNを消す必要もある。
 * notifyを実行するのと同時にCDN削除リクエストも投げる
 *
 * topicsテーブルのpubish_start、topicsテーブルのpubish_end、
 * blocksテーブルのpubish_start、blocksテーブルのpubish_end
 * の内、現在時間以降のmin時間
 *
 * @return void
 */
	public static function notifyOnTime() {
		//CDNの破棄
		(new NetCommonsCDNCache())->invalidate();

		//DataServerに通知
		self::notify();
	}
}
