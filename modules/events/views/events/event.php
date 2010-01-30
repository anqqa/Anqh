
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

	</article>
</section>
