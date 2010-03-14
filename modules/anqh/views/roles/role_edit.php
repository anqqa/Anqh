<?php
/**
 * Edit role
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

			<?= form::input_wrap('name', $values, 'maxlength="32"', __('Name'), $errors) ?>

			<?= form::input_wrap('description', $values, '', __('Description'), $errors) ?>

		</ul>
	</fieldset>

	<fieldset>
		<?= form::submit(false, __('Save')) ?>
		<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
	</fieldset>

<?= form::close() ?>
