<?php
/**
 * Element of common javascript
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
?>

<div id="nc-flash-message" class="alert alert-<?php echo h($class); ?> alert-dismissable">
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

	<?php if (empty($isDismissed)): ?>
		<?php if ((int)$interval > 0) : ?>
			$('#nc-flash-message').fadeIn(500).delay(<?php echo (int)$interval; ?>).fadeOut(2000);
		<?php else: ?>
			$('#nc-flash-message').fadeIn(500);
		<?php endif; ?>
	<?php else: ?>
		$('#nc-flash-message').fadeOut(0);
	<?php endif; ?>
</script>
