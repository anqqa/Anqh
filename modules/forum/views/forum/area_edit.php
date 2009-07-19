
<?= form::open() ?>

	<fieldset>
		<legend><?= __('Group') ?></legend>
		<ul>

			<?= form::dropdown_wrap('forum_group_id', $form, $values, '', __('Group'), $errors) ?>

			<?= form::input_wrap('name', $values, '', __('Name'), $errors) ?>

			<?= form::input_wrap('description', $values, '', __('Description'), $errors) ?>

			<?= form::input_wrap('sort', $values, '', __('Sort'), $errors) ?>

			<?= form::input_wrap('type', $values, '', __('Type'), $errors) ?>
		</ul>
	</fieldset>

	<fieldset>
		<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
