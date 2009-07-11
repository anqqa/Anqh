
<?php if (empty($tags)): ?>
<div class="notice"><?= Kohana::lang('tags.error_no_tags_found') ?></div>
<?php else: ?>
<ul class="tags">
	<?php foreach ($tags as $tag): ?>
	<li><?= html::anchor(url::model($tag), $tag->name) ?></li>
	<?php endforeach; ?>
</ul>
<?php	endif; ?>
