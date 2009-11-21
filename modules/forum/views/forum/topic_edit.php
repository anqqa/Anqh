<?= form::open() ?>

	<fieldset>
		<legend><h3><?= __('Topic') ?></h3></legend>
		<ul>
			<li>
				<?= html::error($errors, 'name') ?>
				<?= form::input('name', $topic['name'], 'maxlength="100"') ?>
			</li>

			<li>
				<?= html::error($errors, 'post') ?>
				<?= form::label('post', __('Post')) ?>
				<?= form::textarea('post', $post['post'], 'rows="20" cols="25"') ?>
			</li>
		</ul>
	</fieldset>

	<fieldset>
		<?= form::csrf() ?>
		<?= empty($topic['id']) ? '' : form::hidden('id', $topic['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
<?php
echo html::script_source('$(function() { $("#post").markItUp(bbCodeSettings); });');
