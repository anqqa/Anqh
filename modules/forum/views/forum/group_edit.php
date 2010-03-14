<?php
/**
 * Forum group edit
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod group-edit">
	<div>
		<?= form::open() ?>

			<fieldset>
				<ul>

					<?= form::input_wrap('name', $values, '', __('Name'), $errors) ?>

					<?= form::input_wrap('description', $values, '', __('Description'), $errors) ?>

					<?= form::input_wrap('sort', $values, '', __('Sort'), $errors) ?>

				</ul>
			</fieldset>

			<fieldset>
				<?= form::csrf() ?>
				<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
				<?= form::submit(false, __('Save')) ?>
				<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
			</fieldset>

		<?= form::close() ?>
	</div>
</section>
