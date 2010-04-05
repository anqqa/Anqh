<?php
/**
 * Shouts
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<ul>
<?php foreach ($shouts as $shout): ?>

	<li><?= html::time(date::format('HHMM', $shout->created), $shout->created) ?> <?= html::user($shout->author) ?>: <?= html::chars($shout->shout) ?></li>

<?php endforeach; ?>
</ul>

<?php if ($can_shout): ?>

<?= form::open('/shout') ?>
<fieldset class="horizontal">
	<ul>
		<?= form::input_wrap(array('name' => 'shout', 'maxlength' => 300, 'title' => __('Shout')), '', '', '', $errors) ?>
		<li><?= form::submit(false, __('Shout')) ?></li>
	</ul>
	<?= form::csrf() ?>
</fieldset>
<?= form::close() ?>

<?php

// AJAX hooks
	echo html::script_source('
$(function() {

	$("section.shout form").live("submit", function(e) {
		e.preventDefault();
		var shouts = $(this).closest("section.shout");
		$.post($(this).attr("action"), $(this).serialize(), function(data) {
			shouts.replaceWith(data);
		});
		return false;
	});

});
');
endif; ?>
