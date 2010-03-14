<?php
/**
 * Member list
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<ul>
<?php foreach ($users as $date => $date_users): ?>

	<li class="day">

		<header class="unit size1of6">
			<?= html::box_day($date) ?>
		</header>

		<ul class="unit size5of6 lastunit">
		<?php foreach ($date_users as $user): ?>

			<li class="member">
				<?= html::user($user) ?>
			</li>

		<?php endforeach; ?>
		</ul>

		<br clear="both" />
	</li>
<?php endforeach; ?>

</ul>
