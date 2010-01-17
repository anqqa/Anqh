
<article class="venue venue-<?= $venue->id ?>">
	<header>
		<h4><?= html::anchor(url::model($venue), $venue->name) ?></h4>
		<span class="details">
			<?= __('Category :category', array(
				':category' => html::anchor(url::model($venue->venue_category), $venue->venue_category->name, array('title' => $venue->venue_category->description))
			)) ?>
		</span>
	</header>

	<footer>
		<?php if ($venue->default_image_id): ?>
		<?= html::anchor(url::model($venue), html::img($venue->default_image, 'thumb'), array('style' => 'display:block;height:31px;')) ?>
		<?php endif; ?>
	</footer>
</article>
