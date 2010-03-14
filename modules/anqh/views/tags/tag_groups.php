<?php
/**
 * Tag groups
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php if (empty($tag_groups)): ?>
<div class="notice"><?= __('No tag groups found') ?></div>
<?php else: ?>
<ul>
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
