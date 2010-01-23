
<section class="mod event event-<?= $event->id ?>">

	<?php	if ($event->dj): ?>
	<article>
		<header>
			<h3><?= __('Artists') ?></h3>
		</header>

		<?= html::specialchars($event->dj) ?>
	</article>
	<?php	endif; ?>

	<?php if ($event->info): ?>
	<article>
		<header>
			<h3><?= __('Extra info') ?></h3>
		</header>

		<?= BB::factory($event->info)->render() ?>
	</article>
	<?php endif; ?>

	<?php if ($event->users): ?>
	<article>
		<header>
			<h3><?= __('Favorites') ?></h3>
		</header>

		<?php foreach ($event->users->order_by('username', 'ASC')->find_all() as $favorite): ?>
		<?= html::nick($favorite) ?>
		<?php endforeach; ?>

	</article>
	<?php endif; ?>

</section>
