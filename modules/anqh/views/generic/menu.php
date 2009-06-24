<ul class="menu">
<?php	$i = 0; $c = count($items); foreach ($items as $id => $link): ?>
	<li class="menu-<?= $id ?><?= ($i == 0 ? ' first' : '') ?><?= (++$i == $c ? ' last' : '') ?><?= ($selected == $id ? ' selected' : '') ?>"><?= html::anchor($link['url'], $link['text']) ?></li>
<?php	endforeach; ?>
</ul>