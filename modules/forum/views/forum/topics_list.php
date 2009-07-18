
<div id="<?= $id ?>" class="tab<?= isset($class) ? ' ' . $class : '' ?>">
	<h4><?= $title ?></h4>
	<?php	if (empty($topics)): ?>
	<span class="notice"><?= __('No topics found') ?></span>
	<?php else: ?>
	<ul class="topics">

		<?php foreach ($topics as $topic): ?>
		<li class="topic-<?= $topic->id ?>"><?= html::anchor(url::model($topic), $topic->name) ?></li>
		<?php endforeach; ?>

	</ul>
	<?php	endif; ?>
</div>
<?php
widget::add('foot', html::script_source("$('#" . $id . " li').ellipsis();"));
