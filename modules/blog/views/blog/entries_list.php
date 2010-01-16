
<section id="<?= $id ?>" class="mod cut blogentries<?= isset($class) ? ' ' . $class : '' ?>">
	<header>
		<h4><?= $title ?></h4>
	</header>

	<?php	if (empty($entries)): ?>
	<span class="notice"><?= __('No blog entries found') ?></span>
	<?php else: ?>
	<ul>

		<?php foreach ($entries as $entry): ?>
		<li class="blogentry-<?= $entry->id ?>"><?= html::anchor(url::model($entry), $entry->name) ?></li>
		<?php endforeach; ?>

	</ul>
	<?php	endif; ?>

</section>
