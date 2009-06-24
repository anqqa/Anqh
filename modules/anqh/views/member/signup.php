<?= form::open() ?>
<ul class="contentlist signup">

	<li class="group">
	<?= html::box_step(1) ?>
		<p class="prefix-1<?= isset($errors['username']) ? ' error' : '' ?>">
			<?= html::error($errors, 'username') ?>
			<?= form::label('signup_username', Kohana::lang('member.username')) ?>
			<?= form::input(array('name' => 'username', 'id' => 'signup_username'), $values['username'], 'title="' . Kohana::lang('member.username_example') . '" maxlength="' . (int)Kohana::config('auth.username.length_max') . '"') ?>
			<span class="tip"><?= Kohana::lang('member.tip_username', Kohana::config('auth.username.length_min')) ?></span>
		</p>
	</li>

	<li class="group">
	<?= html::box_step(2) ?>
		<p class="prefix-1<?= isset($errors['password']) ? ' error' : '' ?>">
			<?= html::error($errors, 'password') ?>
			<?= form::label('signup_password', Kohana::lang('member.password')) ?>
			<?= form::password(array('name' => 'password', 'id' => 'signup_password')) ?>
			<span class="tip"><?= Kohana::lang('member.tip_password', Kohana::config('auth.password.length_min')) ?></span>
		</p>
	</li>

	<li class="group">
	<?= html::box_step(3) ?>
		<p class="prefix-1">
			<?= html::error($errors, 'email') ?>
			<?= form::label('signup_email', Kohana::lang('member.email')) ?>
			<?= form::input(array('name' => 'email', 'id' => 'signup_email'), $invitation->email, 'title="' . Kohana::lang('member.email_example') . '" disabled="disabled"') ?>
		</p>
	</li>
	
	<li class="group iconless">
		<p class="prefix-1">
			<span class="buttons">
				<?= form::submit('signup_submit', Kohana::lang('member.signup')) ?>
				<?= html::anchor(empty($_SESSION['history']) ? '/' : $_SESSION['history'], Kohana::lang('generic.form_cancel')) ?>
			</span>
			<span class="tip"><?= Kohana::lang('member.tip_signup') ?></span>
		</p>
	</li>
	
</ul>
<?= form::close() ?>
