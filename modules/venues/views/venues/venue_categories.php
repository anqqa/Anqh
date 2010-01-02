
<section class="mod venue-categories">
	<ul class="contentlist grouped">
	<?php foreach ($venue_categories as $venue_category): ?>

		<li class="group">

			<h3><?= html::anchor(url::model($venue_category), $venue_category->name) ?></h3>
			<sup><?= $venue_category->description ?></sup>
			<ul>
			<?php $city_id = false; foreach ($venue_category->venues->find_all() as $venue): if ($venue->city_id == $city_id) continue; $city_id = $venue->city_id; ?>
				<li><?= html::anchor(url::model($venue_category) . '#' . utf8::strtolower($venue->city->city), $venue->city->city) ?></li>
			<?php endforeach;	?>
			</ul>

		</li>
	<?php endforeach; ?>

	</ul>
</section>
