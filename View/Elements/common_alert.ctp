<?php
/**
 * Element of common javascript
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
if (!empty($isDismissed)) {
	$fadeInInterval = 1000;
	$displayflashMess = ' style="display: none;"';
} else {
	$fadeInInterval = 500;
	$displayflashMess = '';
}
?>

<div id="nc-flash-message" class="alert alert-<?php echo h($class); ?> alert-dismissable"<?php echo $displayflashMess; ?>>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<div>
		<?php echo $message; ?>
	</div>
</div>

<script>
	$('.close').click(function() {
		$('#nc-flash-message').stop(true).fadeOut(0);
		return false;
	});

	<?php if ((int)$interval > 0) : ?>
		$('#nc-flash-message').fadeIn(<?php echo (int)$fadeInInterval; ?>).delay(<?php echo (int)$interval; ?>).fadeOut(2000);
	<?php else: ?>
		$('#nc-flash-message').fadeIn(<?php echo (int)$fadeInInterval; ?>);
	<?php endif; ?>
</script>
