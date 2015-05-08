<?php
/**
 * Frame setting layout
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>

<!DOCTYPE html>
<html lang="en" ng-app="NetCommonsApp">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>
			<?php
				if (isset($pageTitle)) {
					echo h($pageTitle);
				}
			?>
		</title>

		<?php
			echo $this->fetch('meta');

			echo $this->Html->css(
				array(
					'/components/bootstrap/dist/css/bootstrap.min.css',
					'/net_commons/css/style.css',
					'/frames/css/style.css',
					'style'
				),
				array('plugin' => false)
			);
			echo $this->fetch('css');

			echo $this->Html->script(
				array(
					'/components/jquery/dist/jquery.min.js',
					'/components/bootstrap/dist/js/bootstrap.min.js',
					'/components/tinymce/tinymce.min.js',
					'/components/angular/angular.min.js',
					'/components/angular-bootstrap/ui-bootstrap-tpls.min.js',
					'/components/angular-ui-tinymce/src/tinymce.js',
					'/net_commons/js/base.js',
					'/frames/js/settings.js'
				),
				array('plugin' => false)
			);
			echo $this->fetch('script');
		?>
	</head>
	<body ng-controller="NetCommons.base">

		<div id="nc-flash-message"
				ng-init="flash.type='hidden'; flash.message='';"
				class="alert {{flash.type}} hidden">
			<button class="close pull-right" type="button" ng-click="flash.close()">
				<span class="glyphicon glyphicon-remove"> </span>
			</button>
			<span class='message'>{{flash.message}}</span>
		</div>

		<header id="nc-system-header">
			<div class="box-site box-id-6">
				<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="/">NetCommons3</a>
						</div>
						<div class="navbar-collapse collapse">
							<ul class="nav navbar-nav">
								<li><a href="/"><?php echo __d('net_commons', 'Home'); ?></a></li>
								<li>
									<?php if ($User = AuthComponent::user()): ?>
										<?php echo $this->Html->link(__d('net_commons', 'Logout'), '/auth/logout') ?>
									<?php else: ?>
										<?php echo $this->Html->link(__d('net_commons', 'Login'), '/auth/login') ?>
									<?php endif; ?>
								</li>
								<li <?php
									if (isset($this->request->params['plugin'])
										&& $this->request->params['plugin'] == 'ThemeSettings') {
									echo 'class="active"';
									}
								?>>
									<?php echo $this->Html->link(__d('net_commons', 'Theme setting'), '/theme_settings/site/') ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</header>

		<div class="container-fluid">
			<div class="col-sm-3">
				<!-- TODO: 左カラム -->
			</div>

			<div class="col-sm-6" ng-controller="FrameSettings" ng-init="initialize({frame: <?php echo h(json_encode($frame)) ?>})">
				<div class="panel panel-{{frame.headerType}}">
					<div class="panel-heading clearfix">
						<?php echo $this->element('Frames.setting_header'); ?>
					</div>
					<div class="panel-body">
						<?php echo $this->fetch('content'); ?>
					</div>
				</div>
			</div>

			<div class="col-sm-3">
				<!-- TODO: 右カラム -->
			</div>
		</div>

		<!-- container-footer  -->
		<footer id="nc-system-footer" role="contentinfo">
			<div class="box-footer box-id-5">
				<div class="copyright">Powered by NetCommons</div>
			</div>
		</footer>
	</body>
</html>