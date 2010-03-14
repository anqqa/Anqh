<?php
/**
 * Tag group edit
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?= form::open() ?>

	<fieldset>
		<ul>
			<li>
				<?= html::error($errors, 'name') ?>
				<?= form::label('name', __('Name')) ?>
				<?= form::input('name', $values['name']) ?>
			</li>

			<li>
				<?= html::error($errors, 'description') ?>
				<?= form::label('description', __('Description')) ?>
				<?= form::input('description', $values['description']) ?>
			</li>

		</ul>
	</fieldset>

	<fieldset>
		<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
