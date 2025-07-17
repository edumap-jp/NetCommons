<?php
/**
 * Element of html footer
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
?>
<footer id="nc-system-footer" role="contentinfo">
	<div class="box-footer">
		<div class="copyright">
			<div style="margin-bottom: 5px; margin-top: 2px;">
				<a style="margin-right: 16px;" href="/terms-of-service">サービス利用規約</a>
				<a href="/privacy">個人情報保護方針</a>
			</div>
			<div class="small">
				<spna>Powered by <a href="https://edumap.jp/" target="_blank">edumap</a></spna>
				<br>
				<span class="small">（<a href="https://www.s4e.jp/" target="_blank">教育のための科学研究所</a>,
					<a href="https://www.nttdata.com/jp/ja/" target="_blank">ＮＴＴデータ</a>,
					<a href="https://www.sakura.ad.jp/" target="_blank">さくらインターネット</a>）</span>
			</div>
		</div>
	</div>
</footer>

<script type="text/javascript">
$(function() {
	$(document).on('keypress', 'input:not(.allow-submit)', function(event) {
		return event.which !== 13;
	});
	$('article > blockquote').css('display', 'none');
	$('<button class="btn btn-default nc-btn-blockquote"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span> </button>')
		.insertBefore('article > blockquote').on('click', function(event) {
			$(this).next('blockquote').toggle();
		});
});
</script>
