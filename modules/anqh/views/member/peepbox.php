
<section class="mod peepbox">
	<header>
		<h4><?= text::title($member->username) ?></h4>
	</header>

	<article>
		<?php if (valid::url($member->picture)): ?>
		<?= html::image($member->picture, array('width' => 160)); ?>
		<?php endif; ?>

		<?php if ($member->default_image_id): ?>
		<?= html::img($member->default_image, 'normal', array('width' => 160)) ?>
		<?php endif; ?>
	</article>

</section>
