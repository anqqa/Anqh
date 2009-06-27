<?= form::open() ?>

	<fieldset>
		<legend><h3><?= __('Post') ?></h3></legend>
		<ul>
			<li>
				<?= html::error($errors, 'post') ?>
				<?= form::textarea('post', $post['post'], 'rows="20" cols="25"') ?>
			</li>
		</ul>
	</fieldset>

	<fieldset>
		<?= empty($post['id']) ? '' : form::hidden('id', $post['id']) ?>
		<?= empty($parent_id) ? '' : form::hidden('parent_id', $parent_id) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
