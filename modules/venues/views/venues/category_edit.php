<?php
/**
 * Venue category edit
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod venue-category-edit">
	<div>
		<?= form::open() ?>

			<fieldset>
				<ul>

					<?= form::input_wrap(array('name' => 'name'), $values, '', __('Name'), $errors) ?>

					<?= form::input_wrap(array('name' => 'description'), $values, '', __('Description'), $errors) ?>

					<?= form::dropdown_wrap('tag_group_id', $form, $values, '', __('Tag group'), $errors) ?>

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
