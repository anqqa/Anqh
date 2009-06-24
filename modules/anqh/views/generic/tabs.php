<div class="tabs" id="<?= $id ?>">
	<ul>
<?php	foreach ($tabs as $tab): ?>
		<li><a href="<?= $tab['href'] ?>"><?= $tab['title'] ?></a></li>
<?php	endforeach; ?>
	</ul>
<?php	foreach ($tabs as $tab): ?>
	<?= $tab['tab'] ?>
<?php	endforeach; ?>
</div>
<?php widget::add('foot', html::script_source('$(function() { $("#' . $id . '").tabs({ fx: { height: "toggle", opacity: "toggle", duration: "fast" } }); });'));