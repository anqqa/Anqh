
<?= form::open_multipart() ?>

	<fieldset>
		<legend><?= __('Picture') ?></legend>
		<ul>

			<?= form::upload_wrap('image', null, null, __('Picture'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('Basic Info') ?></legend>
		<ul>

			<?= form::input_wrap('name', $values, 'maxlength="50"', __('Name'), $errors) ?>

			<?= form::input_wrap('dob', $values, 'maxlength="10"', __('Date of Birth'), $errors) ?>

			<?= form::dropdown_wrap('gender', array('' => '', 'm' => __('Male'), 'f' => __('Female')), $values, '', __('Gender'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('Location') ?></legend>
		<ul>

			<?= form::input_wrap('address_street', $values, 'maxlength="50"', __('Address'), $errors) ?>

			<?= form::input_wrap('address_city', $values, 'maxlength="50"', __('City'), $errors) ?>

			<?= form::input_wrap('address_zip', $values, 'maxlength="5"', __('Zip code'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::hidden('city_id', $values['city_id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
