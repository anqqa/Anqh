
<section class="mod venue-info">

	<?php if ($venue->default_image_id): ?>
	<article class="logo">
		<?= html::img($venue->default_image, 'normal', array('title' => __('Logo'), 'width' => '100%')) ?>
	</article>
	<?php endif; ?>

	<?php	if ($venue->homepage || $venue->description || $venue->info || $venue->hours || $venue->tags): ?>
	<article class="information">
		<header>
			<h3><?= __('Basic information') ?></h3>
		</header>

		<dl>
			<?= empty($venue->homepage)    ? '' : '<dt>' . __('Homepage')      . '</dt><dd>' . html::anchor($venue->homepage) . '</dd>' ?>
			<?= empty($venue->description) ? '' : '<dt>' . __('Description')   . '</dt><dd>' . html::chars($venue->description) . '</dd>' ?>
			<?= empty($venue->info)        ? '' : '<dt>' . __('Extra info')    . '</dt><dd>' . html::chars($venue->info) . '</dd>' ?>
			<?= empty($venue->hours)       ? '' : '<dt>' . __('Opening hours') . '</dt><dd>' . html::chars($venue->hours) . '</dd>' ?>
			<?= empty($venue->tags)        ? '' : '<dt>' . __('Tags')          . '</dt><dd>' . implode(', ', $venue->tags->select_list('id', 'name')) . '</dd>' ?>
		</dl>
	</article>
	<?php	endif; ?>

	<article class="contact">
		<header>
			<h3><?= __('Contact information') ?></h3>
		</header>

		<?php if ($venue->address || $venue->city): ?>
		<dl class="address">
			<dt><?= __('Address') ?></dt>
			<dd>
				<address>
					<?= html::chars($venue->address) ?><br />
					<?= html::chars($venue->zip) ?><br />
					<?= html::chars($venue->city_name) ?><br />
				</address>
			</dd>

			<?php if ($venue->latitude && $venue->longitude): ?>
			<dd>
				<?= html::anchor('#map', __('Toggle map')) ?>
			</dd>
			<?php endif; ?>

		</dl>
			<?php if ($venue->latitude && $venue->longitude): ?>
		<div id="map" style="display: none"><?= __('Map loading') ?></div>
				<?php
					$map = new Gmap('map', array('ScrollWheelZoom' => true));
					$map->center($venue->latitude, $venue->longitude, 15)->controls('small')->types();
					$map->add_marker(
						$venue->latitude, $venue->longitude,
						'<strong>' . html::chars($venue->name) . '</strong><p>' . html::chars($venue->address) . '<br />' . html::chars($venue->zip) . ' ' . html::chars($venue->city_name) . '</p>'
					);
					widget::add('foot', html::script_source($map->render('gmaps/jquery_event')));
					widget::add('foot', html::script_source("$('.contact a[href=#map]').click(function() { $('#map').toggle('normal', gmap_open); return false; });"));
				?>
			<?php endif; ?>
		<?php endif; ?>

	</article>

	<?php if (count($venue->images->find_all()) > 1): ?>
	<article class="pictures lightboxed">
		<header>
			<h3><?= __('Pictures') ?></h3>
		</header>

		<?php foreach ($venue->images->find_all() as $image): if ($image->id != $venue->default_image_id): ?>
			<?= html::anchor($image->url('normal'), html::img($image, 'thumb',__('Picture')), array('title' => html::chars($venue->name))) ?>
		<?php endif; endforeach; ?>

	</article>
	<?php endif; ?>

</section>

<div class="lightbox" id="slideshow">
	<a class="prev" title="<?= __('Previous') ?>">&laquo;</a>
	<a class="next" title="<?= __('Next') ?>">&raquo;</a>
	<a class="action close" title="<?= __('Close') ?>">&#10006;</a>
	<div class="info"></div>
</div>
<?php
echo html::script_source('
$(function() {
	$(".lightboxed a").overlay({
		effect: "apple",
		target: "#slideshow",
		expose: {
			color: "#222",
			loadSpeed: 200,
			opacity: 0.75
		}
	}).gallery({
		template: "<strong>${title}</strong> <span class=\"details\">' . __('Image ${index} of ${total}') . '</span>"
	});
});
');

