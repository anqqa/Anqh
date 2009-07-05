<div class="side birthdays">
	<h4><?= __('Birthdays') ?></h4>
	<?php	if (empty($birthdays)): ?>
	<span class="notice"><?= __('No birthdays today') ?></span>
	<?php	else: foreach ($birthdays as $age => $birthday): $users = array(); ?>
	<h5><?= __(':years years', array(':years' => $age)) ?></h5>
	<?php	foreach ($birthday as $user) $users[] = html::user($user->id, $user->username); ?>
	<?= join(', ', $users); ?>
	<?php endforeach; endif; ?>
</div>