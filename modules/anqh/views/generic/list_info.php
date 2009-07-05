<div id="<?= $id ?>" class="tab">
	<h4><?= $title ?></h4>
	<?php if (!empty($actions)): ?>
		<div class="actions">
		<?php foreach ($actions as $action): ?>
			<?= html::anchor($action['link'], $action['text'], array('class' => 'action')) ?>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<dl>
		<?php foreach ($list as $term => $definition): ?>
		<dt><?= $term ?>:</dt><dd><?= $definition ?></dd>
		<?php endforeach; ?>
	</dl>
</div>
