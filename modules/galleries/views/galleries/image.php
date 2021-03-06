<?php
/**
 * Gallery image
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod gallery-image">
	<div>
		<nav>

			<?php if ($previous): ?>
			<?= html::anchor(url::model($gallery) . '/' . $previous->id, '&laquo; ' . __('Previous'), array('title' => __('Previous image'), 'class' => 'prev')) ?>
			<?php else: ?>
			&laquo; <?= __('Previous') ?>
			<?php endif ?>

			<?= __(':current of :total', array(':current' => $current, ':total' => $images)) ?>

			<?php if ($next): ?>
			<?= html::anchor(url::model($gallery) . '/' . $next->id, __('Next') . ' &raquo;', array('title' => __('Next image'), 'class' => 'next')) ?>
			<?php else: ?>
			<?= __('Next') ?> &raquo;
			<?php endif ?>

		</nav>

		<?php if ($next): ?>
		<?= html::anchor(url::model($gallery) . '/' . $next->id, html::image('http://' . Kohana::config('site.image_server') . '/kuvat/' . $gallery->dir . '/pieni_' . $image->legacy_filename), array('title' => __('Next image'))) ?>
		<?php else: ?>
		<?= html::anchor(url::model($gallery), html::image('http://' . Kohana::config('site.image_server') . '/kuvat/' . $gallery->dir . '/pieni_' . $image->legacy_filename), array('title' => __('Back to gallery'))) ?>
		<?php endif ?>

		<?php if ($image->description): ?>
			<?php $names = array(); foreach (explode(',', $image->description) as $name) $names[] = html::user(trim($name)); ?>
		<footer>
			<?= __('In picture: :users', array(':users' => implode(', ', $names))) ?>
		</footer>
		<?php endif ?>
	</div>
</section>
<?php
echo html::script_source('
	$(document).keyup(function(e) {
		var key = e.keyCode || e.which;
		if (e.target.type === undefined) {
			switch (key) {
				case 37: var link = $(".gallery-image a.prev").first().attr("href"); break;
				case 39: var link = $(".gallery-image a.next").first().attr("href"); break;
			}
			if (link) {
				window.location = link;
			}
		}
	});
');
