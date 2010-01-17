
<section class="mod venue venue-<?= $venue->id ?>">

	<?= $venue->default_image_id ? html::image($venue->default_image->url('thumb'), __('Logo')) : '' ?>

	<ul>

		<?php	if ($venue->description || $venue->info || $venue->hours || $venue->tags): ?>
		<li class="information">
			<dl>
				<?= empty($venue->description) ? '' : '<dt>' . __('Description')   . '</dt><dd>' . html::specialchars($venue->description) . '</dd>' ?>
				<?= empty($venue->info)        ? '' : '<dt>' . __('Extra info')    . '</dt><dd>' . html::specialchars($venue->info) . '</dd>' ?>
				<?= empty($venue->hours)       ? '' : '<dt>' . __('Opening hours') . '</dt><dd>' . html::specialchars($venue->hours) . '</dd>' ?>
				<?= empty($venue->tags)        ? '' : '<dt>' . __('Tags')          . '</dt><dd>' . implode(', ', $venue->tags->select_list('id', 'name')) . '</dd>' ?>
			</dl>
		</li>
		<?php	endif; ?>

		<?php if (count($venue->images) > 1): ?>
		<li class="pictures">
			<?php foreach ($venue->images as $image): ?>
				<?php if ($image->id != $venue->default_image_id): ?>
					<?= html::anchor($image->url('normal'), html::img($image, 'thumb')) ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php widget::add('head', html::stylesheet('ui/jquery.lightbox-0.5')) ?>
			<?php widget::add('foot', html::script('js/jquery.lightbox-0.5')) ?>
			<?php widget::add('foot', html::script_source("$(function() { $('li.pictures a').lightBox(); });")) ?>
		</li>
		<?php endif; ?>

		<li class="contact">
			<h3><?= __('Contact') ?></h3>

			<?php if ($venue->address || $venue->city): ?>
			<dl class="grid-3 alpha omega address">
				<dt><?= __('Address') ?></dt>
				<dd>
					<address>
						<?= html::specialchars($venue->address) ?><br />
						<?= html::specialchars($venue->zip) ?><br />
						<?= html::specialchars($venue->city_name) ?><br />
					</address>
				</dd>
				<?php if ($venue->latitude && $venue->longitude): ?>
				<dd>
					<?= html::anchor('#map', __('Toggle map')) ?>
				</dd>
				<?php endif; ?>
			</dl>
			<?php endif; ?>

			<?php if ($venue->homepage): ?>
			<dl class="grid-3 alpha omega internet">
				<dt><?= __('Homepage') ?></dt>
				<dd><?= html::anchor($venue->homepage) ?></dd>
			</dl>
			<?php endif; ?>
		</li>

		<?php if ($venue->latitude && $venue->longitude): ?>
		<li class="grid-7 alpha omega map">
			<div id="map" style="display: none"><?= __('Map loading') ?></div>
			<?php
				$map = new Gmap('map', array('ScrollWheelZoom' => true));
				$map->center($venue->latitude, $venue->longitude, 15)->controls('small')->types();
				$map->add_marker(
					$venue->latitude, $venue->longitude,
					'<strong>' . html::specialchars($venue->name) . '</strong><p>' . html::specialchars($venue->address) . '<br />' . html::specialchars($venue->zip) . ' ' . html::specialchars($venue->city_name) . '</p>'
				);
				widget::add('foot', html::script_source($map->render('gmaps/jquery_event')));
				widget::add('foot', html::script_source("$('.contact a:first').click(function() { $('#map').toggle('normal', gmap_open); return false; });"));
			?>
		</li>
		<?php endif; ?>

	</ul>

</section>
