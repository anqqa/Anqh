<!doctype html>
<html>

<head>
	<meta charset="UTF-8" />
	<title><?= strip_tags($page_title) ?><?= (!empty($page_title) ? ' | ' : '') . Kohana::config('site.site_name') ?></title>
	<link rel="icon" type="image/png" href="/ui/favicon.png" />
	<?= html::stylesheet(array('ui/boot', 'ui/grid', 'ui/typo', 'ui/base', 'ui/site', 'ui/jquery-ui')); ?>
	<?= html::stylesheet($stylesheets) ?>
	<!--[if IE]>
	<?= html::script('http://html5shiv.googlecode.com/svn/trunk/html5.js'); ?>
	<![endif]-->
	<script src="http://www.google.com/jsapi?key=<?= Kohana::config('site.google_api_key') ?>"></script>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); ?>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js') ?>
<?= widget::get('head') ?>
</head>

<body id="<?= $page_id ?>" class="<?= $page_class ?>">

	<!-- HEADER -->

	<header id="header" class="container-1">
		<div class="container-12 clearfix">

<h1><?= html::anchor('/', Kohana::config('site.site_name')) ?></h1>
<?= widget::get('header') ?>
<?= widget::get('navigation') ?>

		</div>
	</header>

	<!-- HEADER -->


	<!-- CONTENT -->

	<section id="content" class="container-1">

		<header class="container-12 clearfix breadcrumb">
			<div class="grid-12">

<?= widget::get('breadcrumb') ?>
<?= widget::get('search') ?>

			</div>
		</header>

		<section class="container-12 clearfix">
			<section id="main-content" class="grid-10-full">

				<header id="title" class="grid-10">

<?= widget::get('actions') ?>

					<h2><?= $page_title ?></h2>
					<p class="subtitle"><?= $page_subtitle ?></p>
<?= widget::get('tabs') ?>
				</header>

				<nav id="submenu" class="grid-10 nav">

<?= widget::get('subnavigation') ?>

				</nav>

<?= $content ?>

			</section>


			<!-- SIDE ADS -->

			<section id="right-ads" class="grid-2">

<?= widget::get('side_ads') ?>

			</section>

			<!-- /SIDE ADS -->

		</section>
	</section>

	<!-- /CONTENT -->


	<!-- DOCK -->

	<section id="dock" class="container-1">
		<div class="container-12 clearfix">
			<div class="grid-6">

<?= widget::get('dock') ?>

			</div>
			<div class="grid-6 extra-actions">

<?= widget::get('dock2') ?>

			</div>
		</div>
	</section>

	<!-- /DOCK -->


	<!-- FOOTER -->

	<footer id="footer" class="container-1">
		<section class="container-12 clearfix section">

<?= widget::get('navigation') ?>
<?= widget::get('footer') ?>


		</section>
		<section id="end" class="container-12 clearfix section">

<?= widget::get('end') ?>

		</section>
	</footer>

	<!-- /FOOTER -->


<?= html::script(array('js/jquery.autocomplete.pack', 'js/jquery.form', 'js/jquery.text-overflow')) ?>

<script>
//<![CDATA[
$(function() {

	// Hook form input hints
	$('input:text, textarea, input:password').hint('hint');

	// Hook delete confirmations
	function confirm_delete(title, action) {
		if (title === undefined) title = 'Are you sure you want to do this?';
		if (action === undefined) action = function() { return true; }
		if ($('#dialog-confirm').length == 0) {
			$('body').append('<div id="dialog-confirm" title="<?= __('Are you sure?') ?>">' + title + '</div>');
			$('#dialog-confirm').dialog({
				modal: true,
				close: function(ev, ui) { $(this).remove(); },
				buttons: {
					'<?= __('No, cancel') ?>': function() { $(this).dialog('close'); },
					'<?= __('Yes, delete') ?>': function() { $(this).dialog('close'); action(); }
				}
			});
		} else {
			$('#confirm-dialog').dialog('open');
		}
	}

	$('a[class*="-delete"]').click(function(e) {
		e.preventDefault();
		var action = $(this);
		var title = action.text();
		if (action.is('a')) {
			confirm_delete(title, function() { window.location = action.attr('href'); });
		} else {
			confirm_delete(title, function() { action.parent('form').submit(); });
		}
	});

});
//]]>
</script>

<?= widget::get('foot') ?>

</body>
</html>
