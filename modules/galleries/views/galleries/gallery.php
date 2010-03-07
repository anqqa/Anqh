
<section class="mod gallery">
	<ul>

		<?php foreach ($gallery->find_images() as $image): ?>

			<li class="unit size1of3">
				<div class="thumb">
					<?= html::anchor(url::model($gallery) . '/' . $image->id, html::image('http://' . Kohana::config('site.image_server') . '/kuvat/' . $gallery->dir . '/thumb_' . $image->legacy_filename)) ?>
				</div>
			</li>

		<?php endforeach; ?>

	</ul>
</section>
