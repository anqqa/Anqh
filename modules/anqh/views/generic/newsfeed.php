
<section class="mod newsfeed">
	<ul>
		<?php foreach ($newsfeed as $item): ?>

			<li class="clearfix">
				<?= html::avatar($item['user']->avatar, $item['user']->username) ?>
				<?= html::user($item['user']) ?>
				<?= $item['text'] ?>
				<?= __(':ago ago', array(':ago' => html::time(date::timespan_short($item['stamp']), $item['stamp']))) ?>
			</li>
		<?php endforeach; ?>

	</ul>
</section>
