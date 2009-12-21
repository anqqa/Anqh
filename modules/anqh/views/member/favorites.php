<ul class="contentlist favorites events">
	<?php foreach ($favorites as $favorite): ?>
	<li class="event-<?= $favorite->id ?>">
		<?= date::format('DDMMYYYY', $favorite->start_time) ?>
		<?= html::anchor(url::model($favorite), text::title($favorite->name), array('title' => html::specialchars($favorite->name))) ?>
	</li>
	<?php endforeach; ?>
</ul>
