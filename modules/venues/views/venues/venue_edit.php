<section class="mod venue-edit">
	<?= form::open_multipart() ?>

		<fieldset>
			<legend><?= __('What?') ?></legend>
			<ul>
				<li>
					<?= html::error($errors, 'name') ?>
					<?= form::label('name', __('Name')) ?>
					<?= form::input('name', $values['name'], 'maxlength="100"') ?>
				</li>

				<li>
					<?= html::error($errors, 'venue_category_id') ?>
					<?= form::label('venue_category_id', __('Category')) ?>
					<?= form::dropdown('venue_category_id', $form['venue_category_id'], $values['venue_category_id']) ?>
				</li>

				<li>
					<?= html::error($errors, 'homepage') ?>
					<?= form::label('homepage', __('Homepage')) ?>
					<?= form::input('homepage', $values['homepage'], 'maxlength="100"') ?>
				</li>

				<li>
					<?= html::error($errors, 'logo') ?>
					<?= form::label('logo', __('Logo')) ?>
					<?= form::upload('logo') ?>
				</li>

				<li class="horizontal">
					<?= form::checkbox('event_host', '1', !empty($values['event_host'])) ?> <?= form::label('event_host', __('Event host')) ?>
				</li>

				<li>
					<?= html::error($errors, 'description') ?>
					<?= form::label('description', __('Description')) ?>
					<?= form::input('description', $values['description'], 'maxlength="250"') ?>
				</li>

				<li>
					<?= html::error($errors, 'info') ?>
					<?= form::label('info', __('Extra info')) ?>
					<?= form::textarea('info', $values['info'], 'rows="3" cols="25"') ?>
				</li>

				<li>
					<?= html::error($errors, 'hours') ?>
					<?= form::label('hours', __('Opening hours')) ?>
					<?= form::textarea('hours', $values['hours'], 'rows="3" cols="25"') ?>
				</li>

				<li>
					<?= html::error($errors, 'picture1') ?>
					<?= form::label('picture1', __('Pictures')) ?>
					<?= form::upload('picture1') ?>
				</li>

				<li>
					<?= html::error($errors, 'picture2') ?>
					<?= form::upload('picture2') ?>
				</li>

				<li>
					<?= html::error($errors, 'tags') ?>
					<?php if (!empty($form['tags'])): ?>
					<?= form::open_fieldset(array('class' => 'tags')) ?>
					<?= form::legend(__('Tags')) ?>
					<?php foreach ($form['tags'] as $tag_id => $tag): ?>
					<?= form::checkbox(array('id' => 'tag_' . $tag, 'name' => 'tags[' . $tag_id . ']'), $tag, isset($values['tags'][$tag_id])) ?> <?= form::label('tag_' . $tag, $tag) ?>
					<?php endforeach; ?>
					<?= form::close_fieldset(); ?>
					<?php endif; ?>
				</li>
			</ul>
		</fieldset>

		<fieldset>
			<legend><?= __('Where?') ?></legend>
			<ul>

				<li>
					<?= html::error($errors, 'address') ?>
					<?= form::label('address', __('Address')) ?>
					<?= form::input('address', $values['address'], 'maxlength="50"') ?>
				</li>

				<li>
					<?= html::error($errors, 'zip') ?>
					<?= form::label('zip', __('Zip code')) ?>
					<?= form::input('zip', $values['zip'], 'maxlength="5"') ?>
				</li>

				<li>
					<?= html::error($errors, 'city_name', 'city_id') ?>
					<?= form::label('city_name', __('City')) ?>
					<?= form::input('city_name', $values['city_name'], 'maxlength="50"') ?>
					<?= form::hidden('city_id', $values['city_id']) ?>
				</li>

			</ul>
		</fieldset>

		<fieldset>
			<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
			<?= form::csrf() ?>
			<?= form::submit(false, __('Save')) ?>
			<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
		</fieldset>

	<?= form::close() ?>
</section>
