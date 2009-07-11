
<?= form::open() ?>

	<fieldset>
		<ul>
			<li>
				<?= html::error($errors, 'name') ?>
				<?= form::label('name', __('Name')) ?>
				<?= form::input('name', $values['name'], 'maxlength="32"') ?>
			</li>

			<li>
				<?= html::error($errors, 'description') ?>
				<?= form::label('description', __('Description')) ?>
				<?= form::input('description', $values['description'], 'maxlength="255"') ?>
			</li>
		</ul>
	</fieldset>

	<fieldset>
		<?= form::hidden('tag_group_id', $values['tag_group_id']) ?>
		<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
