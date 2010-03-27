<?php
/**
 * Short blog topics list
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<header>
	<h4><?= $title ?></h4>
</header>

<?php	if (empty($entries)): ?>
<span class="notice"><?= __('No blog entries found') ?></span>
<?php else: ?>
<ul>

	<?php foreach ($entries as $entry): ?>
	<li class="blogentry-<?= $entry->id ?>"><?= html::anchor(url::model($entry), $entry->name) ?></li>
	<?php endforeach; ?>

</ul>
<?php	endif; ?>
