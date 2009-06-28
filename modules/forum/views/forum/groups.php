<ul class="grouped">
	<?php foreach ($groups as $group): ?>

	<li class="group clearfix group-<?= $group->id ?>">
		<h3><?= html::anchor(url::model($group), $group->name) ?></h3>
		<p><?= html::specialchars($group->description) ?></p>
		<ul class="contentlist areas">
		<?php foreach ($group->forum_areas as $area): ?>

			<?php if ($area->access_has($this->user, Forum_Area_Model::ACCESS_READ)): ?>
			<li class="area-<?= $area->id ?>">
				<h4>
					<?= html::anchor(url::model($area), text::title($area->name), array('title' => strip_tags($area->description))) ?>
					<span><?= __(':topics topics, :posts posts', array(
					':topics' => '<var>' . num::format($area->topics) . '</var>',
					':posts'  => '<var>' . num::format($area->posts) . '</var>'
				)) ?></span>
				</h4>
				<span class="details">
				<?php if ($area->topics > 0): ?>
				<?= __('Last post in :area by :user :ago ago', array(
					':area' => html::anchor(url::model($area->last_topic), text::limit_chars(text::title($area->last_topic->name), 20, '&hellip;', true), array('title' => html::specialchars($area->last_topic->name))),
					':user' => html::nick(null, $area->last_topic->last_poster),
					':ago'  => '<abbr title="' . date::format('DMYYYY_HM', $area->last_topic->last_posted) . '">' . date::timespan_short($area->last_topic->last_posted) . '</abbr>',
				)) ?>
				<?php else: ?>
				<sup><?= __('No topics found') ?></sup>
				<?php endif; ?>
				</span>
			</li>
			<?php else: ?>
			<li class="area-<?= $area->id ?> disabled">
				<h4>
					<span title="<?= strip_tags($area->description) ?>"><?= text::title($area->name) ?>
				</h4>
				<?= __('Members only') ?>
			</li>
			<?php	endif; ?>

		<?php endforeach; ?>
		</ul>
	</li>

	<?php endforeach; ?>
</ul>
