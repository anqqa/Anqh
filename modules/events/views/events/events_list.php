
<section id="<?= $id ?>" class="mod events<?= isset($class) ? ' ' . $class : '' ?>">
	<header>
		<h4><?= $title ?></h4>
	</header>

	<?php	if (!empty($events)): ?>
	<ul class="article cut events">

		<?php foreach ($events as $event): ?>
		<li class="event event-<?= $event->id ?>">
			<!--<?= date::format('DDMM', $event->start_time) ?> -->
			<!--<?= html::anchor(url::model($event), text::limit_chars(text::title($event->name), 20, '&hellip;', true), array('title' => $event->name)) ?>-->
			<?= html::anchor(url::model($event), $event->name) ?>
		</li>
		<?php endforeach; ?>

	</ul>

	<?php else: ?>

	<span class="notice"><?= __('No events found') ?></span>

	<?php	endif; ?>

</section>
