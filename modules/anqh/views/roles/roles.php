<ul class="roles">
	<?php foreach ($roles as $role): ?>
	<li><?= html::anchor('/role/' . url::title($role->id, $role->name), $role->name) ?> - <?= html::specialchars($role->description) ?></li>
	<?php endforeach; ?>
</ul>
