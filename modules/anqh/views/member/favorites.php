<ul class="contentlist favorites events">
	<?php foreach ($favorites as $favorite): ?>
	<li class="event-<?= $favorite->event->id ?>">
		<?= date::format('DDMMYYYY', $favorite->event->start_time) ?>
		<?= html::anchor(url::model($favorite->event), text::title($favorite->event->name), array('title' => html::specialchars($favorite->event->name))) ?>
	</li>
	<?php endforeach; ?>
</ul>
