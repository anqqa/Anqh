
<section class="mod calendar">
<?php foreach ($events as $date => $cities): ?>

	<div class="line day">

		<header class="unit size1of6">
			<?= html::box_day($date) ?>
		</header>

		<div class="unit size5of6 lastunit cities">
			<?php foreach ($cities as $city => $events): ?>

			<section class="city city-<?= url::title($city) ?>">
				<?php if (!empty($city)): ?>

				<header>
					<h3><?= text::title($city) ?></h3>
				</header>
				<?php endif; ?>

				<section class="events">
					<?php	foreach ($events as $event): ?>

					<article class="event event-<?= $event->id ?>">

						<header>
							<h4><?= html::anchor(url::model($event), text::title($event->name)) ?></h4>

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
						</header>

						<?php if ($event->dj): ?>

						<section>
							<p class="dj"><?= html::specialchars($event->dj) ?></p>
						</section>

						<?php endif; ?>

					</article>

					<?php endforeach; ?>
				</section><!-- event -->

			</section><!-- city -->

			<?php endforeach; ?>
		</div><!-- cities -->

	</div><!-- day -->

<?php endforeach; ?>
</section>
