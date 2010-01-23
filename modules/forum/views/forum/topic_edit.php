
<section class="mod topic-edit">
	<?= form::open() ?>

	<fieldset>
		<ul>

			<?php if ($topics === false): ?>
			<?= form::input_wrap(array('name' => 'name', 'maxlength' => 100), $topic, '', '', $errors) ?>
			<?php else: ?>
			<?= form::dropdown_wrap('bind_id', $topics, $topic, '', __('Topic'), $errors) ?>
			<?php endif; ?>

			<?= form::textarea_wrap(array('name' => 'post', 'id' => 'post', 'rows' => 20, 'cols' => 25), $topic, '', true, __('Post'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::csrf() ?>
		<?= empty($topic['id']) ? '' : form::hidden('id', $topic['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

	<?= form::close() ?>
</section>
<?php
echo html::script_source('$(function() { $("#post").markItUp(bbCodeSettings); });');
