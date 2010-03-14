<?php
/**
 * Tags list
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php if (empty($tags)): ?>
<div class="notice"><?= Kohana::lang('tags.error_no_tags_found') ?></div>
<?php else: ?>
<ul>
	<?php foreach ($tags as $tag): ?>
	<li><?= html::anchor(url::model($tag), $tag->name) ?></li>
	<?php endforeach; ?>
</ul>
<?php	endif; ?>
