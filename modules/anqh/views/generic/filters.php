<?php
/**
 * Data filters
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

if (!empty($filters)): ?>

<?= form::open(null, array('class' => 'filters pills')) ?>

	<?php foreach ($filters as $type => $filter): ?>
	<fieldset>
		<!-- <legend><?= html::specialchars($filter['name']) ?>:</legend>-->
		<ul>
			<li>
				<?= form::checkbox(array('name' => 'filter[]', 'id' => 'all-' . $type), 'all', true) ?>
				<?= form::label('all-' . $type, __('All')) ?>
			</li>
			<?php foreach ($filter['filters'] as $key => $name): ?>
			<li>
				<?= form::checkbox(array('name' => 'filter[]', 'id' => $type . '-' . $key), $type . '-' . $key) ?>
				<?= form::label($type . '-' . $key, $name) ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<?php endforeach ?>

<?= form::close() ?>
<?php
	widget::add('footer', html::script_source("
function filters(all) {
	if (all) {

		// Open all
		$('form.filters input').each(function() {
			$('.' + this.id + ':hidden').slideDown('normal');
		});

	} else {

		// Filter individually
		$('form.filters input').each(function() {
			if ($(this).is(':checked')) {
				$('.' + this.id + ':hidden').slideDown('normal');
			} else {
				$('.' + this.id + ':visible').slideUp('normal');
			}
		});

	}
}

$(function() {

	// Hook clicks
	$('form.filters :checkbox').click(function() {

		var checked = $(this).is(':checked');

		if ($(this).val() != 'all') {

			// Individual filters
			if (checked) {

				// Uncheck 'all'
				$('form.filters input[value=\"all\"]').attr('checked', false);

			}

			// Check 'all' if no other filters
			if ($('form.filters input[value!=\"all\"]').is(':checked') == false) {
				$('form.filters input[value=\"all\"]').attr('checked', 'checked');
				filters(true);
			} else {
				filters();
			}

		} else {

			// All filters
			if (!checked) {
				return false;
			}

			$('form.filters input[value!=\"all\"]').attr('checked', false);
			filters(checked);

		}

	});
});
"));
endif;
