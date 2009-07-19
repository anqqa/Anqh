
<?= form::open() ?>

	<fieldset>
		<legend><?= __('Role') ?></legend>
		<ul>

			<?= form::input_wrap('name', $values, 'maxlength="32"', __('Name'), $errors) ?>

			<?= form::input_wrap('description', $values, 'maxlength="32"', __('Description'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
