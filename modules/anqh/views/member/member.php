<div class="member member-<?= $user->id ?> prefix-1">

	<?php if (valid::url($user->picture)): ?>
	<?= html::image($user->picture); ?>
	<?php endif; ?>

	<?php if ($user->default_image_id): ?>
	<?= html::img($user->default_image) ?>
	<?php endif; ?>

</div>
