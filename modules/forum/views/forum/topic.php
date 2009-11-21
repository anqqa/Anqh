
<section class="mod topic topic-<?= $topic->id ?>">
	<?php foreach ($posts as $post):

		// Viewer's post
		$mine = ($this->user && $post->author_id == $this->user->id);

		// Topic author's post
		$owners = ($post->author_id == $topic->author_id);

		// Time difference between posts
		$current = strtotime($post->created);
		$difference = (isset($previous)) ? date::timespan($current, $previous, 'years,months') : array('years' => 0, 'months' => 0);
		if ($difference['years'] || $difference['months']) {
	?>

	<aside class="post-old"><?= __('Previous post over :ago ago', array(':ago' => date::timespan_short($current, $previous))) ?></aside>

	<?php
		}
		$previous = $current;
	?>

	<article class="post post-<?= $post->id ?> <?= ($owners ? 'owner ' : '') . ($mine ? 'my ' : '') . text::alternate('', 'alt') ?>">
		<header>

			<?= html::avatar($post->author->avatar, $post->author->username) ?>

			<?php if ($mine): ?>

			<span class="actions">
				<?= html::anchor('forum/post/' . $post->id . '/edit',   __('Edit'),   array('class' => 'action post-edit')) ?>
				<?= html::anchor('forum/post/' . $post->id . '/delete/?token=' . csrf::token(), __('Delete'), array('class' => 'action post-delete')) ?>
			</span>

			<?php endif; ?>

			<span class="details">
			<?= __('Written by :user :ago ago',
				array(
					':user' => html::nick($post->author_id, $post->author_name),
					':ago' => '<abbr title="' . date::format('DMYYYY_HM', $post->created) . '">' . date::timespan_short($post->created) . '</abbr>'
				)) ?>
			<?php if ($post->modifies > 0): ?>
			<br />
			<?= __('Edited :ago ago',
				array(
					':ago' => '<abbr title="' . date::format('DMYYYY_HM', $post->modified) . '">' . date::timespan_short($post->modified) . '</abbr>'
				)) ?>
			<?php endif; ?>
			<?php if ($post->parent_id): $parent_topic = $post->parent->forum_topic; ?>
			<br />
			<?= __('Replying to :parent',
				array(
					':parent' => html::anchor(url::model($parent_topic) . '/' . $post->parent_id . '#post-' . $post->parent_id, text::title($parent_topic->name)),
				)) ?>
			<?php endif; ?>
			</span>

		</header>

		<section>

<?= BB::factory($post->post)->render() ?>

		</section>

		<footer>
			<?php if ($this->user && !$topic->read_only): ?>

			<span class="actions">
				<?= html::anchor('forum/post/' . $post->id . '/reply', __('Reply'), array('class' => 'action post-reply')) ?>
				<?= html::anchor('forum/post/' . $post->id . '/quote', __('Quote'), array('class' => 'action post-quote')) ?>
			</span>

			<?php endif; ?>
		</footer>
	</article>

	<?php endforeach; ?>
</section>
