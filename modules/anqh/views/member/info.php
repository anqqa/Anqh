<div id="basic-info" class="container">

	<h4><?= __('Basic Info') ?></h4>
	<dl>
		<?php if (!empty($user->name)): ?>
		<dt><?= __('Name') ?>:</dt><dd><?= html::specialchars($user->name) ?></dd>
		<?php endif; ?>
		<?php if (!empty($user->city_name)): ?>
		<dt><?= __('City') ?>:</dt><dd><?= html::specialchars($user->city_name) ?></dd>
		<?php endif; ?>
		<?php if (!empty($user->dob) && $user->dob != '0000-00-00'): ?>
		<dt><?= _('Date of Birth') ?>:</dt><dd><?= date::format('DMYYYY', $user->dob) ?> (<?= __(':years years', array(':years' => date::timespan(strtotime($user->dob), null, 'years'))) ?>)</dd>
		<?php endif; ?>
		<?php if (!empty($user->gender)): ?>
		<dt><?= __('Gender') ?>:</dt><dd class="<?= ($user->gender == 'm' ? 'male' : 'female') ?>"><?= ($user->gender == 'm' ? __('Male') : __('Female')) ?></dd>
		<?php endif; ?>
		<?php if (!empty($user->latitude) && !empty($user->longitude)): ?>
		<dt><?= __('Location') ?>:</dt><dd><?= $user->latitude ?>, <?= $user->longitude ?></dd>
		<dd><?= html::anchor('#map', __('Toggle map')) ?></dd>
		<dd><div id="map" style="display: none"><?= __('Map loading') ?></div></dd>
		<?php
			$map = new Gmap('map', array('ScrollWheelZoom' => true));
			$map->center($user->latitude, $user->longitude, 15)->controls('small')->types('G_PHYSICAL_MAP', 'add');
			$map->add_marker(
				$user->latitude, $user->longitude,
				'<strong>' . html::specialchars($user->username) . '</strong>'
			);
			widget::add('foot', html::script_source($map->render('gmaps/jquery_event')));
			widget::add('foot', html::script_source("$('a[href*=\"#map\"]:first').click(function() { $('#map').toggle('normal', gmap_open); return false; });"));
		?>
		<?php endif; ?>
	</dl>
</div>

<div id="site-info">
	<h4><?= __('Site Info') ?></h4>
	<dl>
		<dt><?= __('Registered') ?>:</dt><dd><?= date::format('DMYYYY_HM', $user->created) ?></dd>
		<dt><?= __('Logins') ?>:</dt><dd><?= number_format($user->logins, 0) ?> (<?= __(':ago ago', array(':ago' => '<abbr title="' . date::format('DMYYYY_HM', $user->last_login) . '">' . date::timespan_short($user->last_login) . '</abbr>')) ?>)</dd>
		<dt><?= __('Forum posts') ?>:</dt><dd><?= number_format($user->posts, 0) ?></dd>
		<dt><?= __('Profile comments') ?>:</dt><dd><?= number_format($user->commentsleft, 0) ?></dd>
	</dl>
</div>
