
<article class="venue venue-<?= $venue->id ?>">
	<header>
		<h4><?= html::anchor(url::model($venue), $venue->name) ?></h4>
	</header>

	<footer>
		<?php if ($venue->default_image_id): ?>
		<?= html::anchor(url::model($venue), html::img($venue->default_image, 'thumb'), array('style' => 'display:block;height:31px;')) ?>
		<?php endif; ?>
	</footer>
</article>
