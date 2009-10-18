
<section class="mod comments">

	<header>
		<h3><?= __('Comments') ?></h3>

		<?= $pagination ?>

	</header>

	<?= form::open() ?>
	<fieldset class="horizontal">
		<?= form::label('private', '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>', 'class="private"') ?>
		<?= form::checkbox('private', '1', $values['private'] == '1', "onchange=\"$('#comment').toggleClass('private', this.checked)\"") ?>

		<?= html::error($errors, 'comment') ?>
		<?= form::input('comment', $values['comment'], 'maxlength="300"') ?>

		<?= form::submit(false, __('Comment')) ?>
	</fieldset>
	<?= form::close() ?>

	<?php foreach ($comments as $comment):
		if (!$comment->private || $this->user && in_array($this->user->id, array($comment->user_id, $comment->author_id))):

			$classes = array();
			if ($comment->private) {
				$classes[] = 'private';
			}

			// Viewer's post
			if ($this->user && $comment->author_id == $this->user->id) {
				$classes[] = 'my';
				$mine = true;
			} else {
				$mine = false;
			}

			// Topic author's post
			if ($comment->author_id == $comment->user_id) {
				$classes[] = 'owner';
			}
 	?>

	<article<?= ($classes ? ' class="' . implode(' ', $classes) . '"' : '') ?>>

		<header>

			<?= html::avatar($comment->author->avatar, $comment->author->username) ?>

			<?php if ($this->user && $comment->user_id == $this->user->id || $mine): ?>
			<span class="actions">
				<?= html::anchor('/member/comment/' . $comment->id . '/delete', __('Delete'), array('class' => 'action comment-delete')) ?>
			</span>
			<?php endif; ?>

			<?= html::nick($comment->author_id, $comment->author->username) ?>,
			<?= __(':ago ago', array(
				':ago' => html::time(date::timespan_short($comment->created), $comment->created))
			) ?>

		</header>

		<?= $comment->private ? '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>: ' : '' ?>
		<?= html::specialchars($comment->comment) ?>

	</article>

	<?php endif; endforeach; ?>

	<footer>

		<?= $pagination ?>

	</footer>

</section
