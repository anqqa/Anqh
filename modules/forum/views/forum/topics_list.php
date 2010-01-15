
<section id="<?= $id ?>" class="article topics<?= isset($class) ? ' ' . $class : '' ?>">
	<header>
		<h4><?= $title ?></h4>
	</header>

	<?php	if (empty($topics)): ?>
	<span class="notice"><?= __('No topics found') ?></span>
	<?php else: ?>
	<ul>

		<?php foreach ($topics as $topic): ?>
		<li class="topic-<?= $topic->id ?>"><?= html::anchor(url::model($topic) . '/page/last', $topic->name) ?></li>
		<?php endforeach; ?>

	</ul>
	<?php	endif; ?>

</section>
<?php
widget::add('foot', html::script_source("$('#" . $id . " li').ellipsis();"));
