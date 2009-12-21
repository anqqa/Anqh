<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= Kohana::config('site.site_name') ?></title>
	<link rel="icon" type="image/png" href="/ui/favicon.png" />
	<?= html::stylesheet(array('ui/boot.css', 'ui/grid.css', 'ui/typo.css', 'ui/invite.css')); ?>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); ?>
	<?= html::script('js/jquery.form.js') ?>
</head>

<body id="<?= $page_id ?>" class="<?= $page_class ?>">

	<section id="content" class="container-1">
		<div class="container-12">

			<header id="header" class="grid-4 prefix-4">
				<h1><?= Kohana::config('site.site_name') ?></h1>
				<h2><?= __('.. we are in private alpha.') ?></h2>
			</header>

			<section id="login" class="grid-2 prefix-5">
				<?= form::open('sign/in') ?>
				<fieldset>
					<ul>
						<?= form::input_wrap('username', null, 'title="' . __('Username') . '"', __('Username')) ?>
						<?= form::password_wrap('password', null, 'title="' . __('Password') . '"', __('Password')) ?>
						<li><?= form::submit('submit', __('Sign in')) ?></li>
					</ul>
				</fieldset>
				<?= form::close() ?>
			</section>

			<section id="invitation" class="grid-2 prefix-5">
				<?= form::open('sign/up') ?>
				<fieldset>
					<ul>
						<?= form::input_wrap('code', null, 'title="' . __('Invitation code') . '" maxlength="32"', __('Got invited?')) ?>
						<li><?= form::submit('invitation_check', __('Sign up')) ?></li>
					</ul>
				</fieldset>
				<?= form::close() ?>
			</section>

		</div>
	</section>

<?= html::script_source("$('input:text, input:password').hint('hint');") ?>
<?= widget::get('foot') ?>

</body>
</html>
