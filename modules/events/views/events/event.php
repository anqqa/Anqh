
<section class="mod event event-<?= $event->id ?>">
	<article>

		<?php	if ($event->dj): ?>
		<header>
			<h3><?= __('Artists') ?></h3>
		</header>

		<?= html::specialchars($event->dj) ?>
		<?php	endif; ?>

		<?php if ($event->info): ?>
		<header>
			<h3><?= __('Extra info') ?></h3>
		</header>

		<?= BB::factory($event->info)->render() ?>
		<?php endif; ?>

		<?php if ($event->users): ?>
		<header>
			<h3><?= __('Favorites') ?></h3>
		</header>

		<?php foreach ($event->users->order_by('username', 'ASC')->find_all() as $favorite): ?>
		<?= html::nick($favorite) ?>
		<?php endforeach; ?>

		<?php endif; ?>

	</article>
</section>
