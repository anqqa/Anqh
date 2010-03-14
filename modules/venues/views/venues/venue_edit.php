<?php
/**
 * Venue edit
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod venue-edit">
	<div>
		<?= form::open_multipart() ?>

			<fieldset>
				<legend><?= __('Basic information') ?></legend>
				<ul>

					<?= form::dropdown_wrap('venue_category_id', $form, $values, '', __('Category'), $errors) ?>

					<?= form::input_wrap(array('name' => 'name', 'maxlength' => 100), $values, '', __('Name'), $errors) ?>

					<?= form::input_wrap(array('name' => 'homepage', 'maxlength' => 100, 'title' => 'http://'), $values, '', __('Homepage'), $errors) ?>

					<li class="horizontal">
						<?= form::checkbox('event_host', '1', !empty($values['event_host'])) ?> <?= form::label('event_host', __('Event host')) ?>
					</li>

				</ul>
			</fieldset>

			<fieldset>
				<legend><?= __('Contact information') ?></legend>
				<ul>

					<?= form::input_wrap(array('name' => 'address', 'maxlength' => 50), $values, '', __('Address'), $errors) ?>

					<?= form::input_wrap(array('name' => 'zip', 'maxlength' => 5, 'length' => 5), $values, '', __('Zip code'), $errors) ?>

					<?= form::input_wrap(array('name' => 'city_name', 'maxlength' => 50), $values, '', __('City'), $errors) ?>

				</ul>
			</fieldset>

			<fieldset>
				<legend><?= __('Detailed information') ?></legend>
				<ul>

					<?= form::upload_wrap('logo', '', '', __('Logo'), $errors) ?>
					<?= $values['default_image_id'] ? '<li>' . html::img(new Image_Model($values['default_image_id']), 'thumb') . '</li>' : '' ?>

					<?= form::input_wrap(array('name' => 'description', 'maxlength' => 250), $values, '', __('Short description'), $errors) ?>

					<?= form::textarea_wrap(array('name' => 'info', 'rows' => 3, 'cols' => 25), $values, '', true, __('Long description'), $errors) ?>

					<?= form::textarea_wrap(array('name' => 'hours', 'rows' => 3, 'cols' => 25), $values, '', true, __('Opening hours'), $errors) ?>

					<?= form::upload_wrap('picture1', '', '', __('Pictures'), $errors) ?>

					<?= form::upload_wrap('picture2', '', '', '', $errors) ?>

					<?php if (!empty($form['tags'])): ?>
					<?= form::checkboxes_wrap('tags', $form, $values, __('Tags'), $errors, 'pills') ?>
					<?php endif; ?>

				</ul>
			</fieldset>

			<fieldset>
				<?= form::csrf() ?>
				<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
				<?= form::hidden('city_id', $values['city_id']) ?>
				<?= form::submit(false, __('Save')) ?>
				<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
			</fieldset>

		<?= form::close() ?>
	</div>
</section>
