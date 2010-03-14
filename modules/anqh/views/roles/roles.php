<?php
/**
 * Roles list
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<ul>
	<?php foreach ($roles as $role): ?>
	<li><?= html::anchor('/role/' . url::title($role->id, $role->name), $role->name) ?> - <?= html::specialchars($role->description) ?></li>
	<?php endforeach; ?>
</ul>
