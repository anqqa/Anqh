<?php $post_id = (empty($post['id']) ? 'post-new-content' : 'post-' . $post['id'] . '-content'); ?>
<section class="mod post-edit">
	<?= form::open() ?>

	<fieldset>
		<?php if (!request::is_ajax()): ?><legend><h3><?= __('Post') ?></h3></legend><?php endif; ?>
		<ul>

			<?= form::textarea_wrap(array('name' => 'post', 'id' => $post_id, 'rows' => 20, 'cols' => 25), $post, '', true, '', $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::csrf() ?>
		<?= empty($post['id']) ? '' : form::hidden('id', $post['id']) ?>
		<?= empty($parent_id) ? '' : form::hidden('parent_id', $parent_id) ?>
		<?php if (request::is_ajax()): ?>

			<?= form::submit(false, __('Save')) ?>
			<?= html::anchor('forum/post/' . $post['id'], __('Cancel')) ?>
			<?= html::script_source('
$("#post-' . $post['id'] . ' .post-edit form").submit(function(e) {
	e.preventDefault();
	$.post($(this).attr("action"), $(this).serialize(), function(data) {
		$("#post-' . $post['id'] . '").replaceWith(data);
	});
});

$("#post-' . $post['id'] . ' .post-edit a").click(function(e) {
	e.preventDefault();
	$.get($(this).attr("href"), function(data) {
		$("#post-' . $post['id'] . '").html(data);
	});
});
'); ?>

		<?php else: ?>

			<?= form::submit(false, __('Save')) ?>
			<?= html::anchor($_SESSION['history'], __('Cancel')) ?>

		<?php endif; ?>
	</fieldset>

	<?= form::close() ?>
</section>
<?php
echo html::script_source('$(function() { $("#' . $post_id . '").markItUp(bbCodeSettings); });');
