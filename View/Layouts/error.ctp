<?php
/**
 * Error レイアウト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

App::uses('SiteSettingUtil', 'SiteManager.Utility');
?>
<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language') ?>" ng-app="NetCommonsApp">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		<?php echo $code . ' ' . $name; ?> -
		<?php echo SiteSettingUtil::read('App.site_name'); ?>:
	</title>
	<?php
		echo $this->html->meta('icon', '/net_commons/favicon.ico');
		echo $this->fetch('meta');

		echo $this->element('NetCommons.common_css');
		echo $this->fetch('css');

		echo $this->element('NetCommons.common_js');
		echo $this->fetch('script');
	?>
</head>

<body ng-controller="NetCommons.base">
	<?php echo $this->Session->flash(); ?>

	<header id="nc-system-header">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container clearfix text-nowrap">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<?php echo $this->NetCommonsHtml->link(SiteSettingUtil::read('App.site_name'), '/', array('class' => 'navbar-brand')); ?>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<?php if (Current::read('User')) : ?>
							<li class="visible-sm">
								<?php echo $this->NetCommonsHtml->avatarLink(Current::read(), [], [], 'User.id'); ?>
							</li>
							<li class="hidden-sm">
								<?php echo $this->NetCommonsHtml->handleLink(Current::read(), ['avatar' => true], [], 'User'); ?>
							</li>
						<?php endif; ?>

						<?php if (AuthComponent::user()) : ?>
							<li>
								<?php echo $this->NetCommonsHtml->link(__d('net_commons', 'Logout'), '/auth/logout'); ?>
							</li>
						<?php else: ?>
							<?php if (! SiteSettingUtil::read('App.close_site') && SiteSettingUtil::read('AutoRegist.use_automatic_register', false)) : ?>
								<li>
									<?php echo $this->NetCommonsHtml->link(__d('auth', 'Sign up'), '/auth/auto_user_regist/request'); ?>
								</li>
							<?php endif; ?>
							<li>
								<?php echo $this->NetCommonsHtml->link(__d('net_commons', 'Login'), '/auth/login'); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</nav>
	</header>

	<main class="container">
		<?php echo $this->fetch('content'); ?>
	</main>

	<?php echo $this->element('NetCommons.common_footer'); ?>
</body>
</html>
