
<?= form::open_multipart() ?>

	<fieldset>
		<legend><?= __('What?') ?></legend>
		<ul>

			<?= form::input_wrap('name', $values, 'maxlength="100"', __('Name'), $errors) ?>

			<?= form::input_wrap('homepage', $values, 'maxlength="100"', __('Homepage'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('When?') ?></legend>
		<ul>

			<?= form::input_wrap('start_date', $values, 'maxlength="10"', __('Date'), $errors) ?>

			<?= form::input_wrap('start_hour', $values, 'maxlength="5"', __('From'), $errors) ?>

			<?= form::input_wrap('end_hour', $values, 'maxlength="5"', __('To'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('Where?') ?></legend>
		<ul>

			<?= form::input_wrap('venue_name', $values, 'maxlength="100"', __('Venue'), $errors) ?>

			<?= form::input_wrap('city_name', $values, 'maxlength="100"', __('City'), $errors) ?>

			<?= form::input_wrap('age', $values, 'maxlength="2"', __('Age limit'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('Tickets') ?></legend>
		<ul>

			<?= form::input_wrap('price', $values, 'maxlength="5"', __('At the door'), $errors) ?>

			<?= form::input_wrap('price2', $values, 'maxlength="5"', __('Presale'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('Who?') ?></legend>
		<ul>

			<?= form::textarea_wrap('dj', $values, 'rows="5" cols="25"', true, __('DJ'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<legend><?= __('What?') ?></legend>
		<ul>

			<?= form::textarea_wrap('info', $values, 'rows="5" cols="25"', true, __('Information'), $errors) ?>

			<?= form::checkboxes_wrap('tags', $form, $values, __('Tags'), $errors, 'pills') ?>

		</ul>

	</fieldset>

	<fieldset>
		<legend><?= __('Flyers') ?></legend>
		<ul>

			<?= form::upload_wrap('flyer_front', null, null, __('Front'), $errors) ?>

			<?= form::upload_wrap('flyer_back', null, null, __('Back'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::hidden('venue_id', $values['venue_id']) ?>
		<?= form::hidden('city_id', $values['city_id']) ?>
		<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
