<?= form::open() ?>

	<fieldset>
		<ul>
			<li>
				<?= html::error($errors, 'name') ?>
				<?= form::label('name', __('Name')) ?>
				<?= form::input(array('name' => 'name'), $values['name']) ?>
			</li>

			<li>
				<?= html::error($errors, 'description') ?>
				<?= form::label('description', __('Description')) ?>
				<?= form::input(array('name' => 'description'), $values['description']) ?>
			</li>

			<li>
				<?= html::error($errors, 'tag_group_id') ?>
				<?= form::label('tag_group_id', __('Tag group')) ?>
				<?= form::dropdown('tag_group_id', $form['tag_group_id'], $values['tag_group_id']) ?>
			</li>
		</ul>
	</fieldset>

	<fieldset>
		<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
