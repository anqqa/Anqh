<ul class="contentlist topics">
	<?php foreach ($topics as $topic): ?>
	<li class="topic-<?= $topic->id ?>">
		<h4><?= html::anchor(url::model($topic), text::title($topic->name), array('title' => $topic->name)) ?></h4>
		<span class="details">
			<?php if (isset($area)): ?>
			<?= __('In :area.', array(
				':area' => html::anchor(url::model($topic->forum_area), text::title($topic->forum_area->name))
			)) ?>
			<?php endif; ?>
			<?= __('Last post by :user :ago ago. :posts posts, :views views.', array(
				':user'  => html::nick(false, $topic->last_poster),
				':ago'   => '<abbr title="' . date::format('DMYYYY_HM', $topic->last_posted) . '">' . date::timespan_short($topic->last_posted) . '</abbr>',
				':posts' => '<var>' . num::format($topic->posts) . '</var>',
				':views' => '<var>' . num::format($topic->reads) . '</var>',
			)) ?>
		</span>
	</li>
	<?php endforeach; ?>
</ul>
