<?php
/**
 * Forum groups
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod groups">
	<div>
		<?php foreach ($groups as $group): ?>

		<section class="group group-<?= $group->id ?>">

			<header>
				<h3><?= html::anchor(url::model($group), $group->name) ?></h3>
				<p><?= html::specialchars($group->description) ?></p>
			</header>

			<?php foreach ($group->forum_areas->find_all() as $area): ?>

				<?php if ($area->has_access(Forum_Area_Model::ACCESS_READ)): ?>

				<article class="area area-<?= $area->id ?>">
					<header>
						<h4 class="unit size2of3"><?= html::anchor(url::model($area), text::title($area->name), array('title' => strip_tags($area->description))) ?></h4>
						<ul class="details unit size1of3">
							<li class="unit size1of2"><?= html::icon_value(array(':topics' => $area->topics), ':topics topic', ':topics topics', 'topics') ?></li>
							<li class="unit size1of2"><?= html::icon_value(array(':posts' => $area->posts), ':posts post', ':posts posts', 'posts') ?></li>
						</ul>
					</header>
					<footer>
						<?php if ($area->topics > 0): ?>
						<?= __('Last post in :area by :user :ago ago', array(
							':area' => html::anchor(url::model($area->last_topic) . '/page/last#last', text::limit_chars(text::title($area->last_topic->name), 20, '&hellip;', true), array('title' => html::specialchars($area->last_topic->name))),
							':user' => html::user(null, $area->last_topic->last_poster),
							':ago'  => html::time(date::timespan_short($area->last_topic->last_posted), $area->last_topic->last_posted),
						)) ?>
						<?php else: ?>
						<sup><?= __('No topics found') ?></sup>
						<?php endif; ?>
					</footer>
				</article>

				<?php elseif (!$area->is_type(Forum_Area_Model::TYPE_HIDDEN)): ?>

				<article class="area area-<?= $area->id ?> disabled">
					<header>
						<h4>
							<span title="<?= strip_tags($area->description) ?>"><?= text::title($area->name) ?>
						</h4>
						<?= __('Members only') ?>
					</header>
				</article>

				<?php	endif; ?>

			<?php endforeach; ?>
		</section>

		<?php endforeach; ?>
	</div>
</section>
