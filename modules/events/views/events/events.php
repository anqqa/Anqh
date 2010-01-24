
<section class="mod calendar">
	<ol class="days">

<?php foreach ($events as $date => $cities):
	$sunrise = date_sunrise(strtotime($date), null, 60.182, 24.954);
	$sunset = date_sunset(strtotime($date), null, 60.182, 24.954); ?>
		<li class="day">

			<header class="line">
				<?= html::box_day($date, false, 'unit size1of6') ?>
				<span class="details">
					<?= __('Sun rises at :sunrise, sun sets at :sunset', array(
						':sunrise' => html::time(date::format('HHMM', $sunrise), $sunrise),
						':sunset' => html::time(date::format('HHMM', $sunset), $sunset)
					)) ?>
				</span>
			</header>

			<?php foreach ($cities as $city => $events): ?>

			<div class="city city-<?= url::title($city) ?>">
				<?php if (!empty($city)): ?>

				<header>
					<h3><?= text::title($city) ?></h3>
				</header>
				<?php endif; ?>

				<?php	foreach ($events as $event): ?>

				<article class="event event-<?= $event->id ?>">

					<header>
						<h4><?= html::anchor(url::model($event), text::title($event->name)) ?></h4>
					</header>

					<?php if ($event->price !== null && $event->price != -1): ?>
					<span class="details price"><?= ($event->price == 0 ? __('Free entry') : '<var>' . $event->price . html::specialchars(Kohana::config('locale.currency.symbol')) . '</var>') ?></span>
					<?php endif; ?>

					<?php if ($event->venue_id): ?>
					<span class="details venue">@ <?= html::anchor(url::model($event->venue), $event->venue->name) ?></span>
					<?php elseif ($event->venue_name): ?>
					<span class="details venue">@ <?= html::specialchars($event->venue_name) ?></span>
					<?php endif; ?>

					<?php if ($event->age && $event->age != -1): ?>
					<span class="details age">(<?= __('Age limit :limit', array(':limit' => '<var>' . $event->age . '</var>')) ?>)</span>
					<?php endif; ?>

					<?php if ($event->dj): ?>
					<div class="dj"><?= html::specialchars($event->dj) ?></div>
					<?php endif; ?>

				</article><!-- /event -->

				<?php endforeach; ?>

			</div><!-- /city -->
			<?php endforeach; ?>

		</li><!-- /day -->
<?php endforeach; ?>

	</ol><!-- /days -->
</section>
