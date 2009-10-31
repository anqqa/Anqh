
<section class="mod facebook-profile">
<?php if (isset($external_user) && isset($fb_uid) && $fb_uid == $external_user->id): // User has 3rd party account and is mapped ?>

	<header>
		<h3><?= __('Facebook settings') ?></h3>
	</header>

	<p>
		<fb:profile-pic uid="loggedinuser" size="normal" facebook-logo="true"></fb:profile-pic>
		<fb:name uid="loggedinuser" useyou="false" linked="true"></fb:name>
	</p>

<?php elseif (isset($fb_uid)): // Logged in but not connected or connected with other acoount ?>

	<?= form::open() ?>

	<fieldset>
		<legend><?= __('Connect your Facebook account') ?></legend>

		<div class="avatar"><fb:profile-pic uid="loggedinuser" size="square" facebook-logo="true"></fb:profile-pic></div>
		<fb:name uid="loggedinuser" useyou="false" linked="true"></fb:name>

		<p>
			<?= __('You have not connected your Facebook account with us yet but you are logged in to Facebook.') ?>
			<?= __('Would you like to use this account to connect us?') ?>
		</p>
		<p>
			<?= form::hidden('connect', User_External_Model::PROVIDER_FACEBOOK) ?>
			<?= form::submit(false, __('Connect')) ?>
		</p>
	</fieldset>

	<?= form::close() ?>

<?php else: // Not logged in ?>

	<header>
		<h3><?= __('Facebook settings') ?></h3>
	</header>

	<p>
		<?= __('You are not logged into Facebook. Would you like to login and connect your account with us?') ?>
		<?= FB::fbml_login() ?>
	</p>

<?php endif; ?>

</section>
