<?php if (!empty($tabs)): ?>
<nav class="tabs">
	<ul>

	<?php $count = 0; foreach ($tabs as $id => $tab): ?>
		<li class="tab-<?= $id ?><?= ($count == 0 ? ' first' : '') ?><?= (++$count == count($tabs) ? ' last' : '') ?><?= ($selected == $id ? ' selected' : '') ?>"><?= html::anchor($tab['link'], $tab['text']) ?></li>
	<?php endforeach; ?>

	</ul>
</nav>
<?php endif; ?>