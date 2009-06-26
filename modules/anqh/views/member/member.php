<div class="member member-<?= $user->id ?> prefix-1">

	<?php if (valid::url($user->picture)): ?>
	<?= html::image($user->picture); ?>
	<?php endif; ?>
	
	<?php if ($user->default_image_id): ?>
	<?= html::image($user->default_image->url('normal'), Kohana::lang('members.picture')) ?>	
	<?php endif; ?>
	
</div>
