
<nav>
	<ul>
	<?php	foreach ($items as $id => $link): ?>
		<li class="menu-<?= $id ?><?= ($selected == $id ? ' selected' : '') ?>"><?= html::anchor($link['url'], $link['text']) ?></li>
	<?php	endforeach; ?>
	</ul>
</nav>
<?php
/*
echo html::script_source('
	$("#header li:not(li li)").hover(function() {
		if (!$(this).hasClass("selected")) {
			$("#header ul li.selected:not(li li)").addClass("unhover");
		}
	}, function() {
		if (!$(this).hasClass("selected")) {
			$("#header ul li.selected:not(li li)").removeClass("unhover");
		}
	});
');
*/
