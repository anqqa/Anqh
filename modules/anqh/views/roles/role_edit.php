<?= form::open() ?>
<p>
	<?= html::error($errors, 'name') ?>
	<?= form::label('name', Kohana::lang('generic.name')) ?>
	<?= form::input('name', $values['name'], 'maxlength="32"') ?>

	<?= html::error($errors, 'description') ?>
	<?= form::label('description', Kohana::lang('generic.description')) ?>
	<?= form::input('description', $values['description'], 'maxlength="255"') ?>
</p>
	
<p>
	<?= form::submit(false, Kohana::lang('generic.form_save')) ?>
	<?= html::anchor($_SESSION['history'], Kohana::lang('generic.form_cancel')) ?>
</p>
<?= form::close() ?>	
