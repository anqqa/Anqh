
<section class="mod area-edit">
	<?= form::open() ?>

		<fieldset>
			<ul>

				<?= form::dropdown_wrap('forum_group_id', $form, $values, '', __('Group'), $errors) ?>

				<?= form::input_wrap('name', $values, '', __('Name'), $errors) ?>

				<?= form::input_wrap('description', $values, '', __('Description'), $errors) ?>

				<?= form::input_wrap('sort', $values, '', __('Sort'), $errors) ?>

				<?= form::checkboxes_wrap('area_type', $form, $values, __('Area type'), $errors) ?>

				<?= form::dropdown_wrap('bind', $form, $values, '', __('Bind to'), $errors) ?>

			</ul>
		</fieldset>

		<fieldset>
			<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
			<?= form::submit(false, __('Save')) ?>
			<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
		</fieldset>

	<?= form::close() ?>
</section>
