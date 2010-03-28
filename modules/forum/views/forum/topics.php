<?php
/**
 * Forum topics list
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php foreach ($topics as $topic): ?>

<article class="topic-<?= $topic->id ?>">
	<header>
		<h4 class="unit size2of3"><?= html::anchor(url::model($topic) . '/page/last#last', text::title($topic->name)) ?></h4>
		<ul class="details unit size1of3">
			<li class="unit size1of2"><?= html::icon_value(array(':views' => $topic->reads), ':views view', ':views views', 'views') ?></li>
			<li class="unit size1of2"><?= html::icon_value(array(':replies' => $topic->posts - 1), ':replies reply', ':replies replies', 'posts') ?></li>
		</ul>
	</header>
	<footer>
		<?php if (isset($area)): ?>
		<?= __('In :area.', array(
			':area' => html::anchor(url::model($topic->forum_area), text::title($topic->forum_area->name), array('title' => strip_tags($topic->forum_area->description)))
		)) ?>
		<?php endif; ?>
		<?= __('Last post by :user :ago ago', array(
			':user'  => html::nick(false, $topic->last_poster),
			':ago'   => html::time(date::timespan_short($topic->last_posted), $topic->last_posted),
		)) ?>
	</footer>
</article>

<?php endforeach; ?>
