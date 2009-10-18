
<div class="mod tabs" id="<?= $id ?>">
	<ul>
		<?php	$t = 0; $selected = 0; foreach ($tabs as $tab): $selected = !empty($tab['selected']) ? $t : $selected; $t++; ?>
		<li><?= html::anchor($tab['href'], $tab['title']) ?></li>
		<?php	endforeach; ?>
	</ul>
	<?php	foreach ($tabs as $tab) echo $tab['tab']; ?>
</div>
<?php
// Initialize tabs immediately to aviod ugly jumping
echo html::script_source('$("#' . $id . '").tabs({ selected: ' . $selected . ', collapsible: true, fx: { height: "toggle", opacity: "toggle", duration: "fast" } });');
// widget::add('foot', html::script_source('$(function() { $("#' . $id . '").tabs({ selected: ' . $selected . ', collapsible: true, fx: { height: "toggle", opacity: "toggle", duration: "fast" } }); });'));
// widget::add('foot', html::script_source('$(function() { $("#' . $id . ' ul").tabs("#' . $id . ' > div", { initialIndex: ' . $selected . ', effect: "fade" }); });'));
