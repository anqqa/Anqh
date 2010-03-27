<?php
/**
 * Forum groups short list
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<header>
	<h4><?= $title ?></h4>
</header>

<ul class="forum">
<?php	foreach ($groups as $group): ?>

	<li class="group">
		<h5><?= html::anchor(url::model($group), text::title($group->name)) ?></h5>
		<ul class="areas">

		<?php	foreach ($group->forum_areas->find_all() as $area): ?>
			<?php if ($area->has_access(Forum_Area_Model::ACCESS_READ)): ?>
			<li><?= html::anchor(url::model($area), text::title($area->name), array('title' => strip_tags($area->description))) ?></li>
			<?php endif; ?>
		<?php endforeach; ?>

		</ul>
	</li>

<?php endforeach; ?>
</ul>
