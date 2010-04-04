<?php
/**
 * Venues
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php if (empty($venues)): ?>

<?= __('No venues found') ?>

<?php else: ?>

	<?php	foreach ($venues as $city => $city_venues): ?>
		<header class="city">
			<h3 id="<?= html::specialchars(mb_strtolower($city)) ?>"><?= html::chars($city) ?></h3>
		</header>

		<?php foreach ($city_venues as $venue): ?>
		<?= View::factory('venues/venue_mini', array('venue' => $venue)) ?>
		<?php endforeach; ?>

	<?php	endforeach; ?>

<?php endif; ?>
