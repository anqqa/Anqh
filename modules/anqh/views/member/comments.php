<h3><?= __('Comments') ?></h3>
<div class="comments">

		<?= form::open() ?>
		<fieldset class="horizontal">
			<?= form::label('private', '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>', 'class="private"') ?>
			<?= form::checkbox('private', '1', $values['private'] == '1', "onchange=\"$('#comment').toggleClass('private', this.checked)\"") ?>

			<?= html::error($errors, 'comment') ?>
			<?= form::input('comment', $values['comment'], 'maxlength="300"') ?>

			<?= form::submit(false, __('Comment')) ?>
		</fieldset>
		<?= form::close() ?>

		<?= $pagination ?>

		<ul class="contentlist">
		<?php foreach ($comments as $comment): ?>
			<?php if (!$comment->private || $this->user && in_array($this->user->id, array($comment->user_id, $comment->author_id))): ?>
			<li class="clearfix<?= $comment->private ? ' private' : '' ?>">

				<?php if ($this->user && $comment->user_id == $this->user->id || $comment->author_id == $this->user->id): ?>
				<?= html::anchor('/member/comment/' . $comment->id . '/delete', __('Delete'), array('class' => 'hidden')) ?>
				<?php endif; ?>

				<?= html::avatar($comment->author->avatar, $comment->author->username) ?>

				<div class="prefix-1 comment">
					<?= html::nick($comment->author_id, $comment->author->username) ?>,
					<?= __(':ago ago', array(
						':ago' => '<abbr title="' . date::format('DMYYYY_HM', $comment->created) . '">' . date::timespan_short($comment->created) . '</abbr>')
					) ?>
					<br />
					<?= $comment->private ? '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>: ' : '' ?>
					<?= html::specialchars($comment->comment) ?>
				</div>

			</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>

		<?= $pagination ?>

</div>
