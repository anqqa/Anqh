
<?php if (empty($tag_groups)): ?>
<div class="prefix-1 notice"><?= __('No tag groups found') ?></div>
<?php else: ?>
<ul class="taggroups contentlist">
	<?php foreach ($tag_groups as $tag_group): ?>
	<li class="group clearfix">

		<ul class="tags-<?= $tag_group->id ?>">
			<li>
				<h3><?= html::anchor(url::model($tag_group), $tag_group->name) ?></h3>
				<sup><?= $tag_group->description ?></sup><br />
				<?php foreach ($tag_group->tags as $tag): ?>
				<?= html::anchor(url::model($tag), $tag->name) ?>
				<?php endforeach; ?>
			</li>
		</ul>

	</li>
	<?php endforeach; ?>
</ul>
<?php	endif; ?>
