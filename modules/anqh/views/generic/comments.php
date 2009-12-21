
<section class="mod comments">

	<header>
		<h3><?= __('Comments') ?></h3>

		<?= $pagination ?>

	</header>

	<?= form::open() ?>
	<fieldset class="horizontal">
		<ul>
			<?php if ($private): ?>
			<?= form::checkbox_wrap('private', '1', $values, "onchange=\"$('#comment').toggleClass('private', this.checked)\"", '<abbr class="private" title="' . __('Private comment') . '">' . __('Priv') . '</abbr>') ?>
			<?php endif; ?>

			<?= form::input_wrap('comment', '', 'maxlength="300"', '', $errors) ?>

			<li><?= form::submit(false, __('Comment')) ?></li>
		</ul>
		<?= form::csrf() ?>
	</fieldset>
	<?= form::close() ?>

	<ul>
	<?php foreach ($comments as $comment):
		$classes = array('line');

		if (!$comment->private || $user && in_array($user->id, array($comment->user_id, $comment->author_id))):

			if ($comment->private) {
				$classes[] = 'private';
			}

			// Viewer's post
			if ($user && $comment->author_id == $user->id) {
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

		<li class="<?= implode(' ', $classes) ?>">

			<?php if ($user && $comment->user_id == $user->id || $mine): ?>
			<span class="actions">
				<?= html::anchor(sprintf($delete, $comment->id), __('Delete'), array('class' => 'action comment-delete')) ?>
			</span>
			<?php endif; ?>

			<?= html::avatar($comment->author->avatar, $comment->author->username) ?>

			<?= html::nick($comment->author_id, $comment->author->username) ?>,
			<?= __(':ago ago', array(
				':ago' => html::time(date::timespan_short($comment->created), $comment->created))
			) ?>
			<br />

			<?= $comment->private ? '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>: ' : '' ?>
			<?= text::smileys(text::auto_link_urls(html::specialchars($comment->comment))) ?>

		</li>

	<?php endif; endforeach; ?>
	</ul>

	<footer>

		<?= $pagination ?>

	</footer>

</section
