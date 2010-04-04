<?php
/**
 * Short forum topics list
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php	if (empty($topics)): ?>
<span class="notice"><?= __('No topics found') ?></span>
<?php else: ?>
<ul>

	<?php foreach ($topics as $topic): ?>
	<li class="topic-<?= $topic->id ?>"><?= html::anchor(url::model($topic) . '/page/last#last', $topic->name) ?></li>
	<?php endforeach; ?>

</ul>
<?php	endif; ?>
