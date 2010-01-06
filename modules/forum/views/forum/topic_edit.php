
<section class="mod topic-edit">
	<?= form::open() ?>

	<fieldset>
		<legend><h3><?= __('Topic') ?></h3></legend>
		<ul>

			<?= form::input_wrap(array('name' => 'name', 'maxlength' => 100), $topic, '', '', $errors) ?>

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
