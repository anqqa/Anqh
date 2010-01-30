
<section class="mod event-info">

	<?php if ($event->flyer_front_image_id || $event->flyer_back_image_id): ?>
	<article class="flyers">
		<?= $event->flyer_front_image_id ? html::img($event->flyer_front, 'normal', array('title' => __('Flyer, front'), 'width' => '100%')) : '' ?>
		<?= $event->flyer_back_image_id  ? html::img($event->flyer_back,  'normal', array('title' => __('Flyer, back'),  'width' => '100%')) : '' ?>
	</article>
	<?php endif; ?>

	<?php if ($event->flyer_front_url || $event->flyer_back_url): ?>
	<article class="flyers">
		<?= $event->flyer_front_url ? html::image($event->flyer_front_url, array('alt' => __('Flyer, front'), 'title' => __('Flyer, front'), 'width' => '100%')) : '' ?>
		<?= $event->flyer_back_url  ? html::image($event->flyer_back_url,  array('alt' => __('Flyer, back'),  'title' => __('Flyer, back'),  'width' => '100%')) : '' ?>
	</article>
	<?php endif; ?>

	<article class="information">
		<header>
			<h3><?= __('Event information') ?></h3>
		</header>

		<dl>
			<?php if (!empty($event->homepage)): ?>
			<dt><?= __('Homepage') ?></dt><dd><?= html::anchor($event->homepage) ?></dd>
			<?php endif; ?>

			<dt><?= __('Opening hours') ?></dt><dd><?= $event->end_time ?
					__('From :from to :to', array(
						':from' => html::time(date::format('HHMM', $event->start_time), $event->start_time),
						':to'   => html::time(date::format('HHMM', $event->end_time), $event->end_time))
					) :
					__('From :from onwards', array(
						':from' => html::time(date::format('HHMM', $event->start_time), $event->start_time))
					) ?></dd>

			<?php if ($event->venue_id): ?>
			<dt><?= __('Venue') ?></dt><dd><?= html::anchor(url::model($event->venue), $event->venue->name) ?>, <?= html::specialchars($event->venue->city_name) ?></dd>
			<?php elseif ($event->venue_name): ?>
			<dt><?= __('Venue') ?></dt><dd><?= ($event->venue_url ?
				html::anchor($event->venue_url, $event->venue_name) :
				html::specialchars($event->venue_name)) .
				($event->city_name ? ', ' . html::specialchars($event->city_name) : '') ?></dd>
			<?php elseif ($event->city_name): ?>
			<dt><?= __('City') ?></dt><dd><?= html::specialchars($event->city_name) ?></dd>
			<?php endif; ?>

			<?php if (!empty($event->age)): ?>
			<dt><?=  __('Age limit') ?></dt><dd><?= __(':years years', array(':years' => '<var>' . $event->age . '</var>')) ?></dd>
			<?php endif; ?>

			<?php if ($event->price == 0): ?>
			<dt><?= __('Tickets') ?></dt><dd><?= __('Free entry') ?></dd>
			<?php elseif ($event->price > 0): ?>
			<dt><?= __('Tickets') ?></dt>
			<dd><?= __(':price by the door', array(':price' => '<var>' . format::money($event->price, $event->country->currencycode) . '</var>')) ?></dd>
			<?= $event->price2 !== null ? '<dd>' . __('presale :price', array(':price' => '<var>' . format::money($event->price2) . '</var>')) . '</dd>' : '' ?>
			<?php endif; ?>

			<?php if (count($event->tags->find_all())): ?>
			<dt><?= __('Music') ?></dt><dd><?php foreach ($event->tags->find_all() as $tag): ?><?= $tag->name ?> <?php endforeach; ?></dd>
			<?php endif; ?>
			<?php if (!empty($event->music)): ?>
			<dt><?= __('Music') ?></dt><dd><?= $event->music ?></dd>
			<?php endif; ?>

		</dl>
	</article>

	<?php if ($event->find_favorites()): ?>
	<article>
		<header>
			<h3><?= __('Favorites') ?></h3>
		</header>

		<?= View::factory('generic/users', array('viewer' => $user, 'users' => $event->find_favorites())) ?>

	</article>
	<?php endif; ?>

</section>
