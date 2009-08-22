<?php if (!empty($actions)): ?>
<div class="actions">
	<?php foreach ($actions as $action): ?>
		<?php
			$attributes = $action;
			unset($attributes['link'], $attributes['text']);
			$attributes['class'] = isset($attributes['class']) ? 'action ' . $attributes['class'] : 'action';
		?>
		<?= html::anchor($action['link'], $action['text'], $attributes) ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>