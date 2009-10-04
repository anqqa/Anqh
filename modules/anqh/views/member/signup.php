
<?= form::open() ?>

	<?= html::box_step(3) ?>
	<fieldset class="prefix-1">
		<legend><?= __('Almost there!') ?></legend>
		<ul>

			<?= form::input_wrap(
				array('name' => 'username', 'id' => 'signup_username'),
				$values,
				'title="' . __('JohnDoe') . '" maxlength="' . (int)Kohana::config('visitor.username.length_max') . '"',
				__('Username'),
				$errors,
				__('Choose a unique username with at least <var>:length</var> characters. No special characters, thank you.', array(':length' => Kohana::config('visitor.username.length_min')))
			) ?>

			<?= form::password_wrap(
				array('name' => 'password', 'id' => 'signup_password'),
				$values,
				'title="' . __('j0hnd03ru13z!') . '"',
				__('Password'),
				$errors,
				__('Try to use letters, numbers and special characters for a stronger password, with at least <var>:length</var> characters.', array(':length' => Kohana::config('visitor.password.length_min')))
			) ?>

			<?= form::input_wrap(
				array('name' => 'email', 'id' => 'signup_email'),
				$invitation->email,
				'title="' . __('john.doe@domain.tld') . '" disabled="disabled"',
				__('Email'),
				$errors
			) ?>

		</ul>
	</fieldset>

	<fieldset class="prefix-1">
		<?= form::submit(false, __('Sign up!')) ?>
		<?= html::anchor(empty($_SESSION['history']) ? '/' : $_SESSION['history'], __('Cancel')) ?>

		<p class="tip">
			<?= __('By signing up, you accept the <a href=":terms">Tems of Use</a> and <a href=":privacy">Privacy Policy</a>.', array(':terms' => '/terms', ':privacy' => '/privacy')) ?>
		</p>
	</fieldset>

<?= form::close() ?>
