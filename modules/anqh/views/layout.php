<!doctype html>
<html lang="<?= $language ?>">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= strip_tags($page_title) ?><?= (!empty($page_title) ? ' | ' : '') . Kohana::config('site.site_name') ?></title>
	<link rel="icon" type="image/png" href="/ui/favicon.png" />
	<?= html::stylesheet(array('ui/boot.css', 'ui/grid.css', 'ui/typo.css', 'ui/base.css')) ?>
	<?= less::stylesheet($skin, false, false, $skin_imports) ?>
	<!--[if IE]>
	<?= html::script('http://html5shiv.googlecode.com/svn/trunk/html5.js'); ?>
	<![endif]-->
	<script src="http://www.google.com/jsapi?key=<?= Kohana::config('site.google_api_key') ?>"></script>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); ?>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js') ?>
<?= widget::get('head') ?>
</head>

<body id="<?= $page_id ?>" class="<?= $page_width ?> <?= $page_class ?>">

	<!-- HEADER -->

	<header id="header">

		<div class="section header">

<h1><?= html::anchor('/', Kohana::config('site.site_name')) ?></h1>
<?= widget::get('header') ?>
<?= widget::get('navigation') ?>

		</div>
		<div class="section breadcrumb">
			<div class="unit size3of5">

<?= widget::get('breadcrumb') ?>

			</div>
			<div class="unit size2of5 last-unit">

<?= widget::get('search') ?>

			</div>
		</div>

	</header>

	<!-- /HEADER -->


	<!-- BODY -->

	<div id="body">

		<div class="section">


			<!-- SIDE ADS -->

			<section id="side-ads" class="unit size1of6">

<?= widget::get('side_ads') ?>

			</section>

			<!-- /SIDE ADS -->


			<!-- MAIN CONTENT -->

			<section id="main" class="unit size1of2">

				<header id="title" class="line">

<?= widget::get('actions') ?>

					<h2><?= $page_title ?></h2>
					<p class="subtitle"><?= $page_subtitle ?></p>

<?= widget::get('tabs') ?>

				</header>

<?= widget::get('main') ?>

			</section>

			<!-- /MAIN CONTENT -->


			<!-- SIDE CONTENT -->

			<aside id="side" class="unit size1of3">

<?= widget::get('side') ?>

			</aside>

			<!-- /SIDE CONTENT -->


		</div>

	</div>

	<!-- /BODY -->


	<!-- DOCK -->

	<div id="dock">
		<div class="section">
			<div class="unit size1of2">

<?= widget::get('dock') ?>

			</div>
			<div class="unit size1of2 lastunit extra-actions">

<?= widget::get('dock2') ?>

			</div>
		</div>
	</div>

	<!-- /DOCK -->


	<!-- FOOTER -->

	<footer id="footer">
		<div class="section">

<?= widget::get('navigation') ?>
<?= widget::get('footer') ?>

		</div>
		<div id="end" class="section">

<?= widget::get('end') ?>

		</div>
	</footer>

	<!-- /FOOTER -->


<?= html::script(array('js/jquery.autocomplete.pack.js', 'js/jquery.form.js', 'js/jquery.text-overflow.js')) ?>

<script>
//<![CDATA[
$(function() {

	// Hook form input hints
	$('input:text, textarea, input:password').hint('hint');


	// Hook delete confirmations
	function confirm_delete(title, action) {
		if (title === undefined) title = '<?= __('Are you sure you want to do this?') ?>';
		if (action === undefined) action = function() { return true; }
		if ($('#dialog-confirm').length == 0) {
			$('body').append('<div id="dialog-confirm" title="' + title + '"><?= __('Are you sure?') ?></div>');
			$('#dialog-confirm').dialog({
				modal: true,
				close: function(ev, ui) { $(this).remove(); },
				buttons: {
					'<?= __('No, cancel') ?>': function() { $(this).dialog('close'); },
					'<?= __('Yes, do it!') ?>': function() { $(this).dialog('close'); action(); }
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
		if (action.data('action')) {
			confirm_delete(title, function() { action.data('action')(); });
		} else if (action.is('a')) {
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
