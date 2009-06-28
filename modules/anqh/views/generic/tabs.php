<div class="tabs" id="<?= $id ?>">
	<ul>
		<?php	$t = 0; $selected = 0; foreach ($tabs as $tab): $selected = !empty($tab['selected']) ? $t : $selected; $t++; ?>
		<li><?= html::anchor($tab['href'], $tab['title']) ?></li>
		<?php	endforeach; ?>
	</ul>
	<?php	foreach ($tabs as $tab): ?>
	<?= $tab['tab'] ?>
	<?php	endforeach; ?>
</div>
<?php
widget::add('foot', html::script_source('$(function() { $("#' . $id . '").tabs({ selected: ' . $selected . ', collapsible: true, fx: { height: "toggle", opacity: "toggle", duration: "fast" } }); });'));
