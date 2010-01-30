
<section class="mod calendar">
	<ol class="days">

<?php foreach ($events as $date => $cities): ?>
		<li class="day">

			<header>
				<?= html::box_day($date) ?>
			</header>

			<?php foreach ($cities as $city => $events): ?>

			<div class="city city-<?= url::title($city) ?>">
				<?php if (!empty($city)): ?>

				<!--<header>
					<h3><?= text::title($city) ?></h3>
				</header>-->
				<?php endif; ?>

				<?php	foreach ($events as $event): ?>

				<article class="event event-<?= $event->id ?>">

					<header>
						<h4><?= html::anchor(url::model($event), text::title($event->name)) ?></h4>
					</header>

					<?php if ($event->price !== null && $event->price != -1): ?>
					<span class="details price"><?= ($event->price == 0 ? __('Free entry') : '<var>' . format::money($event->price, $event->country->currencycode) . '</var>') ?></span>
					<?php endif; ?>

					<?php if ($event->venue_id): ?>
					<span class="details venue">@ <?= html::anchor(url::model($event->venue), $event->venue->name) ?>, <?= html::chars($event->venue->city->name) ?></span>
					<?php elseif ($event->venue_name || $event->city_name): ?>
					<span class="details venue">@ <?= html::chars($event->venue_name) . ($event->venue_name && $event->city_name ? ', ' : '') . html::chars($event->city_name) ?></span>
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

			<br clear="all" />
		</li><!-- /day -->
<?php endforeach; ?>

	</ol><!-- /days -->
</section>
