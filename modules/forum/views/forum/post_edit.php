
<section class="mod post-edit">
	<?= form::open() ?>

	<fieldset>
		<legend><h3><?= __('Post') ?></h3></legend>
		<ul>

			<?= form::textarea_wrap(array('name' => 'post', 'id' => 'post'), $post, 'rows="20" cols="25"', true, '', $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::csrf() ?>
		<?= empty($post['id']) ? '' : form::hidden('id', $post['id']) ?>
		<?= empty($parent_id) ? '' : form::hidden('parent_id', $parent_id) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

	<?= form::close() ?>
</section>
<?php
echo html::script_source('$(function() { $("#post").markItUp(bbCodeSettings); });');
