<?php
/**
 * Current::initialize()のControllerテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCurrentLibTestUtility', 'NetCommons.TestSuite');
App::uses('CurrentLibControllerTestExpectedData', 'NetCommons.Test/Fixture/CurrentLib');
App::uses('CurrentLibControllerTestPostData', 'NetCommons.Test/Fixture/CurrentLib');
App::uses('CurrentLib', 'NetCommons.Lib');
App::uses('Current', 'NetCommons.Utility');

/**
 * Current::initialize()のControllerテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\Test\Case\Lib\CurrentLib
 */
class NetCommonsLibCurrentLibControllerAdministratorTest extends ControllerTestCase {

/**
 * By default, all fixtures attached to this class will be truncated and reloaded after each test.
 * Set this to false to handle manually
 *
 * @var array
 */
	public $autoFixtures = false;

/**
 * Called when a test case method is about to start (to be overridden when needed.)
 *
 * @param string $method Test method about to get executed.
 * @return void
 */
	public function startTest($method) {
		if (! NetCommonsCurrentLibTestUtility::canLoadTables()) {
			$this->markTestSkipped('開発環境でのみテストができます。');
			return;
		}
		NetCommonsCurrentLibTestUtility::loadTables();
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		NetCommonsCurrentLibTestUtility::login('1');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		NetCommonsCurrentLibTestUtility::logout();
		NetCommonsCurrentLibTestUtility::resetCurrentLib();
		parent::tearDown();
	}

/**
 * 管理者でのGETテスト
 *
 * @param string $controller generateするコントローラ
 * @param string $url テストするURL
 * @param array|false $expects 期待値リスト
 * @param string|false $exception Exception文字列
 *
 * @dataProvider dataGetRequestToppageByAdministrator
 * @dataProvider dataGetRequestMyRoomOfAdministratorByAdministrator
 * @dataProvider dataGetRequestMyRoomOfGeneralUser1ByAdministrator
 * @dataProvider dataGetRequestAnnouncementBlockSettingsByAdministrator
 * @dataProvider dataGetRequestPrivatePlanOfAdministoratorByAdministrator
 * @dataProvider dataGetRequestPrivatePlanOfGeneralUser1ByAdministrator
 * @dataProvider dataGetRequestBbsArticleOfCommunityByAdministrator
 * @dataProvider dataGetRequestPublicCalendarPageByAdministrator
 *
 * @return void
 */
	public function testGetRequestByAdministrator($controller, $url, $expects, $exception) {
		$this->generate($controller);
		NetCommonsCurrentLibTestUtility::testControllerGetRequest(
			$this, $url, $expects, $exception
		);
	}

/**
 * パブリックのお知らせページのセッティングモード表示のテスト
 *
 * @return void
 */
	public function testGetRequestAnnouncementPageWithSettingModeByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$controller = 'Pages.Pages';
		$url = '/setting/announcements_page';
		$expects = [
			'assertContains' => array_merge(
				$ExpectedData->getExpectedAnnouncement(['public_1', 'public_2', 'public_3']),
				$ExpectedData->getExpectedFrame(['menu']),
				$ExpectedData->getExpectedMenuList([
					'public', 'private', 'community_1', 'community_2'
				]),
				$ExpectedData->getExpectedSettingMode('off')
			),
			'assertNotContains' => [],
			'assertRegExp' => array_merge([],
				$ExpectedData->getExpectedActiveMenu('public_announcement_page')
			),
		];
		$exception = false;

		//セッティングモードON
		NetCommonsCurrentLibTestUtility::settingMode(true);

		$this->generate($controller);
		NetCommonsCurrentLibTestUtility::testControllerGetRequest(
			$this, $url, $expects, $exception
		);

		//セッティングモードのクリア
		NetCommonsCurrentLibTestUtility::settingMode(null);
	}

/**
 * 管理者でのPOSTテスト
 *
 * @param string $controller generateするコントローラ
 * @param string $url テストするURL
 * @param array $post POSTの内容
 * @param array|false $expects 期待値リスト
 * @param string|false $exception Exception文字列
 *
 * @_dataProvider dataPostRequestAnnouncementOfToppageByAdministrator
 * @_dataProvider dataPostRequestAnnouncementOfPublicPageByAdministrator
 * @_dataProvider dataPostRequestAddCalendarPlanByAdministrator
 * @_dataProvider dataPostRequestEditCalendarPlanByAdministrator
 * @dataProvider dataPostRequestDeleteCalendarPlanByAdministrator
 *
 * @return void
 */
	public function testPostRequestByAdministrator($controller, $url, $post, $expects, $exception) {
		$this->generate($controller, [
			'components' => ['Security'],
		]);
		NetCommonsCurrentLibTestUtility::testControllerPostRequest(
			$this, $url, $post, $expects, $exception
		);
	}

/**
 * Frame追加テスト
 *
 * @param array $post POSTの内容
 * @dataProvider dataPostRequestFrameAdd
 * @return void
 */
	public function testPostRequestFrameAddByAdministrator($post, $expects) {
		$controller = 'Frames.Frames';
		$url = '/frames/frames/add';
		$exception = false;

		$this->generate($controller, [
			'components' => ['Security'],
		]);
		NetCommonsCurrentLibTestUtility::testControllerPostRequest(
			$this, $url, $post, $expects, $exception
		);
	}

/**
 * Frame編集テスト
 *
 * @return void
 */
	public function testPostRequestFrameEditByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$controller = 'Frames.Frames';
		$url = '/frames/frames/edit';
		$post = $PostData->getPostDataByFrameEdit();
		$expects = [
			'Location' =>
				$ExpectedData->getExpectedRedirectAfterPost('public_announcement_page')
		];
		$exception = false;

		$this->generate($controller, [
			'components' => ['Security'],
		]);
		NetCommonsCurrentLibTestUtility::testControllerPostRequest(
			$this, $url, $post, $expects, $exception
		);
	}

/**
 * Frame削除テスト
 *
 * @return void
 */
	public function testPostRequestFrameDeleteByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$controller = 'Frames.Frames';
		$url = '/frames/frames/delete';
		$post = $PostData->getPostDataByFrameDelete();
		$expects = [
			'Location' =>
				$ExpectedData->getExpectedRedirectAfterPost('public_announcement_page_setting_mode')
		];
		$exception = false;

		$_SERVER['HTTP_REFERER'] = '/setting/announcements_page';
		$this->generate($controller, [
			'components' => ['Security'],
		]);
		NetCommonsCurrentLibTestUtility::testControllerPostRequest(
			$this, $url, $post, $expects, $exception
		);
		unset($_SERVER['HTTP_REFERER']);
	}

/**
 * トップページテスト表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestToppageByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'トップページ' => [
				'controller' => 'Pages.Pages',
				'url' => '/',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedAnnouncement(['toppage']),
						$ExpectedData->getExpectedFrame(['menu']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('on')
					),
					'assertNotContains' => [],
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * 管理者のマイルーム表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestMyRoomOfAdministratorByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'管理者のマイルーム' => [
				'controller' => 'Pages.Pages',
				'url' => '/private/private_room_system_admistrator',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedAnnouncement(['private']),
						$ExpectedData->getExpectedFrame(['menu']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('on')
					),
					'assertNotContains' => [],
					'assertRegExp' => array_merge([],
						$ExpectedData->getExpectedActiveMenu('private_administrator')
					),
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * 一般ユーザ1のマイルーム表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestMyRoomOfGeneralUser1ByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'一般ユーザ1のマイルーム' => [
				'controller' => 'Pages.Pages',
				'url' => '/private/private_room_general_user_1',
				'expects' => false,
				'exception' => 'ForbiddenException',
			],
		];

		return $results;
	}

/**
 * お知らせのブロック設定表示[Community room 1]のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestAnnouncementBlockSettingsByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'お知らせのブロック設定表示[Community room 1]' => [
				'controller' => 'Announcements.AnnouncementBlocks',
				'/announcements/announcement_blocks/edit/11?frame_id=16',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedFrame(['menu', 'community_1_announcement_edit_1']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('off'),
						$ExpectedData->getExpectedAnnouncement(['community_1_edit'])
					),
					'assertNotContains' => [],
					'assertRegExp' =>
						$ExpectedData->getExpectedBlockSettingTabs('announcement', 'block_setting')
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * プライベート(管理者)の予定の表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestPrivatePlanOfAdministoratorByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'プライベート(管理者)の予定の表示' => [
				'controller' => 'Calendars.CalendarPlans',
				'/calendars/calendar_plans/view/calendar_event_key_472',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedFrame(['menu']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('on'),
						$ExpectedData->getExpectedCalendarPlanView('private_plan_1')
					),
					'assertNotContains' => [],
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * プライベート(一般ユーザ1)の予定の表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestPrivatePlanOfGeneralUser1ByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'プライベート(一般ユーザ1)の予定の表示' => [
				'controller' => 'Calendars.CalendarPlans',
				'/calendars/calendar_plans/view/calendar_event_key_786?frame_id=11',
				'expects' => false,
				'exception' => 'ForbiddenException',
			],
		];

		return $results;
	}

/**
 * コミュニティの記事詳細表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestBbsArticleOfCommunityByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'コミュニティの記事詳細表示' => [
				'controller' => 'Bbses.BbsArticles',
				'/bbses/bbs_articles/view/15/bbs_article_key_1?frame_id=20',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedFrame(['menu']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_1_bbs_page', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('on'),
						$ExpectedData->getExpectedBbsArticleView('community_1_bbs_article_1')
					),
					'assertNotContains' => [],
					'assertRegExp' => $ExpectedData->getExpectedToBackLink('community_1_bbs_article_1'),
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * パブリックのカレンダーページの表示のテストデータ
 *
 * @return array テストデータ
 */
	public function dataGetRequestPublicCalendarPageByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		$results = [
			'パブリックのカレンダーページの表示' => [
				'controller' => 'Pages.Pages',
				'/calendars_page',
				'expects' => [
					'assertContains' => array_merge(
						$ExpectedData->getExpectedFrame(['menu']),
						$ExpectedData->getExpectedMenuList([
							'public', 'private', 'community_1', 'community_2'
						]),
						$ExpectedData->getExpectedSettingMode('on')
					),
					'assertNotContains' => [],
					'assertRegExp' => array_merge(
						$ExpectedData->getExpectedCalendar([
							'public_plan_1', 'community_plan_1', 'private_plan_1'
						]),
						$ExpectedData->getExpectedActiveMenu('public_calendar_page')
					),
					'assertNotRegExp' => array_merge([],
						$ExpectedData->getExpectedCalendar([
							'private_plan_2'
						])
					),
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * トップページのお知らせ登録のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestAnnouncementOfToppageByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$results = [
			'トップページのお知らせ' => [
				'controller' => 'Announcements.Announcements',
				'url' => '/announcements/announcements/edit/12',
				'post' => $PostData->getPostDataAnnouncement('toppage_announcement'),
				'expects' => [
					'Location' => $ExpectedData->getExpectedRedirectAfterPost('toppage'),
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * パブリックスペースのお知らせ1(Announcements Page)登録のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestAnnouncementOfPublicPageByAdministrator() {
		//@var CurrentLibControllerTestExpectedData
		$ExpectedData = new CurrentLibControllerTestExpectedData();

		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$results = [
			'パブリックスペースのお知らせ1(Announcements Page)' => [
				'controller' => 'Announcements.Announcements',
				'url' => '/announcements/announcements/edit/8?frame_id=12',
				'post' => $PostData->getPostDataAnnouncement('public_announcement_2'),
				'expects' => [
					'Location' => $ExpectedData->getExpectedRedirectAfterPost('public_announcement_page'),
				],
				'exception' => false,
			],
		];

		return $results;
	}

/**
 * フレーム追加のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestFrameAdd() {
		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();
		$results = $PostData->getDataProviderByFrameAdd();
		return $results;
	}

/**
 * カレンダーの予定追加のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestAddCalendarPlanByAdministrator() {
		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$frameTests = [
			'11' => 'フレームあり',
			null => 'フレームなし',
		];

		$results = [];
		foreach ($frameTests as $frameId => $testFrameTitle) {
			$commonSuccessResults = $PostData->getSuccessCommonDataByEditCalendarPlan($frameId);
			$commonFailureResults = $PostData->getFailureCommonDataByEditCalendarPlan($frameId);

			$frameResults = [
				'カレンダーの予定追加(' . $testFrameTitle . '、パブリック)' => array_merge(
					$commonSuccessResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('1', $frameId),
					]
				),
				'カレンダーの予定追加(' . $testFrameTitle . '、管理者のプライベート)' => array_merge(
					$commonSuccessResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('5', $frameId),
					]
				),
				'カレンダーの予定追加(' . $testFrameTitle . '、一般ユーザ1のプライベート)' => array_merge(
					$commonFailureResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('6', $frameId),
					]
				),
				'カレンダーの予定追加(' . $testFrameTitle . '、コミュニティ1)' => array_merge(
					$commonSuccessResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('8', $frameId),
					]
				),
				'カレンダーの予定追加(' . $testFrameTitle . '、コミュニティ2(準備中))' => array_merge(
					$commonSuccessResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('11', $frameId),
					]
				),
				'カレンダーの予定追加(' . $testFrameTitle . '、会員全員)' => array_merge(
					$commonSuccessResults,
					[
						'url' => $PostData->getPostUrlAddCalendarPlan($frameId),
						'post' => $PostData->getPostDataAddCalendarPlan('3', $frameId),
					]
				),
			];

			$results = array_merge($results, $frameResults);
		}

		return $results;
	}

/**
 * カレンダーの予定編集のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestEditCalendarPlanByAdministrator() {
		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$frameTests = [
			'11' => 'フレームあり',
			null => 'フレームなし',
		];
		//$editRrules = [
		//	'0' => 'この予定のみ',
		//	'1' => 'これ以降に指定した全ての予定',
		//	'2' => '設定した全ての予定',
		//];

		$results = [];
		foreach ($frameTests as $frameId => $testFrameTitle) {
			$commonSuccessResults = $PostData->getSuccessCommonDataByEditCalendarPlan($frameId);
			//$commonFailureResults = $PostData->getFailureCommonDataByEditCalendarPlan($frameId);

			$frameResults = [
				'カレンダーの予定編集(' . $testFrameTitle . '、繰り返しなし、パブリック)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlEditCalendarPlan('1100', $frameId),
							'post' => $PostData->getPostDataEditCalendarPlan('1100', '0', $frameId),
						]
					),
				'カレンダーの予定編集(' . $testFrameTitle . '、繰り返しなし、管理者のプライベート)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlEditCalendarPlan('1101', $frameId),
							'post' => $PostData->getPostDataEditCalendarPlan('1101', '0', $frameId),
						]
					),
				'カレンダーの予定編集(' . $testFrameTitle . '、繰り返しなし、コミュニティ1)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlEditCalendarPlan('1101', $frameId),
							'post' => $PostData->getPostDataEditCalendarPlan('1101', '0', $frameId),
						]
					),
			];
			$results = array_merge($results, $frameResults);

			//foreach ($editRrules as $editRrule => $editRruleTile) {
			//	$frameResults = [
			//		'カレンダーの予定編集(' . $testFrameTitle . '、繰り返しあり(' . $editRruleTile . ')、パブリック)' =>
			//			array_merge(
			//				$commonSuccessResults,
			//				[
			//					'url' => $PostData->getPostUrlEditCalendarPlan('2', $frameId),
			//					'post' => $PostData->getPostDataEditCalendarPlan('2', $editRrule, $frameId),
			//				]
			//			),
			//		'カレンダーの予定編集(' . $testFrameTitle . '、繰り返しあり(' . $editRruleTile . ')、管理者のプライベート)' =>
			//			array_merge(
			//				$commonSuccessResults,
			//				[
			//					'url' => $PostData->getPostUrlEditCalendarPlan('472', $frameId),
			//					'post' => $PostData->getPostDataEditCalendarPlan('472', $editRrule, $frameId),
			//				]
			//			),
			//	];
			//	$results = array_merge($results, $frameResults);
			//}
		}

		return $results;
	}

/**
 * カレンダーの予定削除のテストデータ
 *
 * @return array テストデータ
 */
	public function dataPostRequestDeleteCalendarPlanByAdministrator() {
		//@var CurrentLibControllerTestPostData
		$PostData = new CurrentLibControllerTestPostData();

		$frameTests = [
			'11' => 'フレームあり',
			null => 'フレームなし',
		];
		//$editRrules = [
		//	'0' => 'この予定のみ',
		//	'1' => 'これ以降に指定した全ての予定',
		//	'2' => '設定した全ての予定',
		//];

		$results = [];
		foreach ($frameTests as $frameId => $testFrameTitle) {
			$commonSuccessResults = $PostData->getSuccessCommonDataByDeleteCalendarPlan($frameId);
			//$commonFailureResults = $PostData->getFailureCommonDataByEditCalendarPlan($frameId);

			$frameResults = [
				'カレンダーの予定削除(' . $testFrameTitle . '、繰り返しなし、パブリック)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlDeleteCalendarPlan('1100', $frameId),
							'post' => $PostData->getPostDataDeleteCalendarPlan('1100', '0', $frameId),
						]
					),
				'カレンダーの予定削除(' . $testFrameTitle . '、繰り返しなし、管理者のプライベート)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlDeleteCalendarPlan('1101', $frameId),
							'post' => $PostData->getPostDataDeleteCalendarPlan('1101', '0', $frameId),
						]
					),
				'カレンダーの予定削除(' . $testFrameTitle . '、繰り返しなし、コミュニティ1)' =>
					array_merge(
						$commonSuccessResults,
						[
							'url' => $PostData->getPostUrlDeleteCalendarPlan('1101', $frameId),
							'post' => $PostData->getPostDataDeleteCalendarPlan('1101', '0', $frameId),
						]
					),
			];
			$results = array_merge($results, $frameResults);

			//foreach ($editRrules as $editRrule => $editRruleTile) {
			//	$frameResults = [
			//		'カレンダーの予定削除(' . $testFrameTitle . '、繰り返しあり(' . $editRruleTile . ')、パブリック)' =>
			//			array_merge(
			//				$commonSuccessResults,
			//				[
			//					'url' => $PostData->getPostUrlDeleteCalendarPlan('2', $frameId),
			//					'post' => $PostData->getPostDataDeleteCalendarPlan('2', $editRrule, $frameId),
			//				]
			//			),
			//		'カレンダーの予定削除(' . $testFrameTitle . '、繰り返しあり(' . $editRruleTile . ')、管理者のプライベート)' =>
			//			array_merge(
			//				$commonSuccessResults,
			//				[
			//					'url' => $PostData->getPostUrlDeleteCalendarPlan('472', $frameId),
			//					'post' => $PostData->getPostDataDeleteCalendarPlan('472', $editRrule, $frameId),
			//				]
			//			),
			//	];
			//	$results = array_merge($results, $frameResults);
			//}
		}

		return $results;
	}

}
