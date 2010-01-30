
<div class="users">
	<?php

		// Build short (friends) and long (others) user list
		$short = $long = array();
		foreach ($users as $user):
			$user = ($user instanceof User) ? $user : ORM::factory('user')->find_user($user);
			if ($viewer && $viewer->is_friend($user)):
				$short[mb_strtoupper($user->username)] = html::user($user);
			else:
				$long[mb_strtoupper($user->username)] = html::user($user);
			endif;
		endforeach;
		ksort($long);

		// If no friends, pick random from long
		if (empty($short) && !empty($long)):
			$shorts = (array)array_rand($long, min(10, count($long)));
			foreach ($shorts as $move):
				$short[$move] = $long[$move];
				unset($long[$move]);
			endforeach;
		endif;
		ksort($short);

	?>

	<?php if (count($short)): ?>
	<?= implode(', ', $short) ?>
	<?php endif; ?>

	<?php if (count($long)): ?>
	<?= __('and') ?> <?= html::anchor('#users', sprintf(__2('%d other', '%d others', count($long)), count($long)), array('class' => 'expander', 'title' => __('Show all'), 'onclick' => '$(".users .long").toggle(); return false;')) ?>
	<div class="long">
	<?= implode(', ', $long) ?>
	</div>
	<?php endif; ?>

</div>
