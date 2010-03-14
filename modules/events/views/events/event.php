<?php
/**
 * Event
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod event event-<?= $event->id ?>">
	<div>

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

	</div>
</section>
