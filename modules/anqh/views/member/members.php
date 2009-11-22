
<section class="mod members">
	<ul>
	<?php foreach ($users as $date => $date_users): ?>

		<li class="line day">

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

		</li>
	<?php endforeach; ?>

</ul>
</section>
