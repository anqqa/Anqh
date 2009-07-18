
<ol class="grouped calendar">
	<?php foreach ($events as $date => $cities): ?>

	<li class="group clearfix">
		<?= html::box_day($date) ?>
		<ol class="prefix-1 grouped cities">
			<?php foreach ($cities as $city => $events): ?>

			<li class="group clearfix city-<?= url::title($city) ?>">
				<?php if (!empty($city)): ?>
				<h3><?= text::title($city) ?></h3>
				<?php endif; ?>
				<ul class="contentlist events">
					<?php	foreach ($events as $event): ?>
					<li class="event event-<?= $event->id ?>">
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

						<?php if ($event->dj): ?>
						<p class="dj"><?= html::specialchars($event->dj) ?></p>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</li>

			<?php endforeach; ?>
		</ol>
	</li>

	<?php endforeach; ?>
</ol>
