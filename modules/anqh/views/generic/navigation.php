
<nav>
	<ul>
	<?php	foreach ($items as $id => $link): ?>
		<li class="menu-<?= $id ?><?= ($selected == $id ? ' selected' : '') ?>">
			<?= html::anchor($link['url'], $link['text']) ?>
			<?php if (isset($link['submenu'])): ?>
			<ul>
				<?php foreach ($link['submenu'] as $sub_id => $sub_link): ?>
				<li class="sub-<?= $sub_id ?><?= ($selected_sub == $sub_id ? ' selected' : '') ?>"><?= html::anchor($sub_link['url'], $sub_link['text']) ?></a>
				<?php endforeach; ?>
			</ul>
			<?php else: ?>
			<ul class="empty"><li>&nbsp;</li></ul>
			<?php endif; ?>
		</li>
	<?php	endforeach; ?>
	</ul>
</nav>
<?php
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
