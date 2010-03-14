<?php
/**
 * Galleries
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod galleries">
	<div>
		<ul>

		<?php foreach ($galleries as $gallery): ?>
			<li class="unit size1of2">
				<div class="thumb unit size2of5">
					<?= html::anchor(url::model($gallery), html::image('http://' . Kohana::config('site.image_server') . '/kuvat/' . $gallery->dir . '/thumb_' . $gallery->default_image->legacy_filename)) ?>
				</div>
				<header>
					<h4><?= html::anchor(url::model($gallery), text::title($gallery->name)) ?></h4>
					<span class="details">
						<?= html::time(date::format('DMYYYY', $gallery->event_date), $gallery->event_date, true) ?>,
						<?= __2(':images image', ':images images', $gallery->image_count, array(':images' => '<var>' . $gallery->image_count . '</var>')) ?>
					</span>
				</header>
			</li>
		<?php endforeach; ?>

		</ul>
	</div>
</section>
