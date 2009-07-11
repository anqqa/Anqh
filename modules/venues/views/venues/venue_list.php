
<h3<?= (isset($id) ? ' id="' . html::specialchars(utf8::strtolower($id)) . '"' : '') ?>><?= html::specialchars($title) ?></h3>
<ul>
	<?php foreach ($venues as $venue): ?>
	<li class="venue venue-<?= $venue->id ?> grid-2">
		<?= View::factory('venues/venue_mini', array('venue' => $venue)) ?>
	</li>
	<?php endforeach; ?>
</ul>
