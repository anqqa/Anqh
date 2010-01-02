
<?php if (empty($venues)): ?>
<?= __('No venues found') ?>
<?php else: ?>
<ul>

	<?php	foreach ($venues as $city => $city_venues): ?>
	<li class="city clearfix">
		<h3 id="<?= html::specialchars(utf8::strtolower($city)) ?>"><?= html::chars($city) ?></h3>
		<ul>
			<?php foreach ($city_venues as $venue): ?>
			<li class="venue venue-<?= $venue->id ?> grid-2">
				<?= View::factory('venues/venue_mini', array('venue' => $venue)) ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php	endforeach; ?>

</ul>
<?php endif; ?>
