
<section class="mod event event-<?= $event->id ?>">
	<article class="line">

		<header class="unit size1of6">
			<?= html::box_day($event->start_time, true) ?>
		</header>

		<section class="unit size5of6 lastunit">
			<ul>
				<li class="location">

					<h3><?= __('Where?') ?></h3>
					<?php if ($event->end_time): ?>
						<?= __('From :from to :to', array(':from' => '<var>' . date::format('HHMM', $event->start_time) . '</var>', ':to' => '<var>' . date::format('HHMM', $event->end_time) . '</var>')) ?>
					<?php else: ?>
						<?= __('From :from onwards', array(':from' => '<var>' . date::format('HHMM', $event->start_time) . '</var>')) ?>
					<?php endif; ?>

					@

					<?php if ($event->venue_id): ?>
						<?= html::anchor(url::model($event->venue), $event->venue->name) ?>, <?= html::specialchars($event->venue->city_name) ?>
					<?php elseif ($event->venue_name): ?>
						<?= $event->venue_url ? html::anchor($event->venue_url, $event->venue_name) : html::specialchars($event->venue_name) . ($event->city_name ? ', ' . html::specialchars($event->city_name) : '') ?>
					<?php elseif ($event->city_name): ?>
						<?= html::specialchars($event->city_name) ?>
					<?php endif; ?>

					<?php if ($event->age): ?>
					<br /><?= __('Age limit :limit', array(':limit' => '<var>' . $event->age . '</var>')) ?>
					<?php endif; ?>

				</li>

				<?php if ($event->price !== null && $event->price != -1 || $event->price2 !== null): ?>
				<li class="price">

					<h3><?= __('Tickets') ?></h3>
					<?php if ($event->price == 0): ?>
						<?= __('Free entry') ?>
					<?php else: ?>
						<?= __(':price by the door', array(':price' => '<var>' . $event->price . html::specialchars(Kohana::config('locale.currency.symbol')) . '</var>')) ?>
						<?= $event->price2 !== null ? ', ' . __('presale :price', array(':price' => '<var>' . $event->price2 . html::specialchars(Kohana::config('locale.currency.symbol')) . '</var>')) : '' ?>
					<?php endif; ?>

				</li>
				<?php endif; ?>

				<?php	if ($event->dj): ?>
				<li class="dj">

					<h3><?= __('Who?') ?></h3>
					<?= html::specialchars($event->dj) ?>

				</li>
				<?php	endif; ?>

				<?php if ($event->info || $event->music || $event->tags): ?>
				<li class="tags">

					<h3><?= __('What?') ?></h3>
					<?php if ($event->info): ?>
						<?= BB::factory($event->info)->render() ?>
					<?php endif; ?>

					<?php foreach ($event->tags as $tag): ?>
					<?= $tag->name ?>
					<?php endforeach; ?><br />

					<?= $event->music ?>

				</li>
				<?php endif; ?>


				<?php if ($event->favorites): ?>
				<li class="favorites">

					<h3><?= __('Favorites') ?></h3>
					<?php foreach ($event->favorites as $favorite): ?>
					<?= html::nick($favorite->user) ?>
					<?php endforeach; ?>

				</li>
				<?php endif; ?>

			</ul>

			<?= $event->flyer_front_image_id ? '<span class="flyer">' . html::image($event->flyer_front_image->url('thumb'), Kohana::lang('events.event_flyer_front')) . '</span>' : '' ?>
			<?= $event->flyer_back_image_id ? '<span class="flyer">' . html::image($event->flyer_back_image->url('thumb'), Kohana::lang('events.event_flyer_back')) . '</span>' : '' ?>
		</section>

	</article>
</section>
