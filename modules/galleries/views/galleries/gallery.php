<?php
/**
 * Gallery
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<ul>

	<?php foreach ($gallery->find_images() as $image): ?>

	<li class="unit size1of4">
		<div class="thumb">
			<?= html::anchor(url::model($gallery) . '/' . $image->id, html::image('http://' . Kohana::config('site.image_server') . '/kuvat/' . $gallery->dir . '/thumb_' . $image->legacy_filename), array('title' => html::chars($image->description))) ?>
		</div>
		<?= html::icon_value(array(':comments' => $image->comments), ':comments comment', ':comments comments', 'posts') ?>
		<?= html::icon_value(array(':views' => $image->views), ':views view', ':views views', 'views') ?>
	</li>

	<?php endforeach; ?>

</ul>
