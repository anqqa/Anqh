
<section id="<?= $id ?>" class="mod tab">
	<header>
		<h4><?= $title ?></h4>
	</header>

	<ul class="forum">
	<?php	foreach ($groups as $group): ?>

		<li class="group">
			<h5><?= html::anchor(url::model($group), text::title($group->name)) ?></h5>
			<ul class="areas">

			<?php	foreach ($group->forum_areas as $area): ?>
				<?php if ($area->access_has($this->user, Forum_Area_Model::ACCESS_READ)): ?>
				<li><?= html::anchor(url::model($area), text::title($area->name), array('title' => strip_tags($area->description))) ?></li>
				<?php endif; ?>
			<?php endforeach; ?>

			</ul>
		</li>

	<?php endforeach; ?>
	</ul>

</section>
