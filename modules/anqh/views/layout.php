<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $language ?>" lang="<?= $language ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= strip_tags($page_title) ?><?= (!empty($page_title) ? ' | ' : '') . Kohana::config('site.site_name') ?></title>
	<link rel="icon" type="image/png" href="/ui/favicon.png" />
	<?= html::stylesheet(array('ui/boot', 'ui/grid', 'ui/typo', 'ui/base', 'ui/site', 'ui/jquery-ui')); ?>
	<?= html::stylesheet($stylesheets) ?>
	<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= Kohana::config('site.google_api_key') ?>"></script>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'); ?>
	<?= html::script('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js') ?>
<?= widget::get('head') ?>
</head>

<body id="<?= $page_id ?>" class="<?= $page_class ?>">

	<!-- HEADER -->

	<div id="header" class="container-1">
		<div class="container-12 clearfix">

<h1><?= html::anchor('/', Kohana::config('site.site_name')) ?></h1>
<?= widget::get('header') ?>
<?= widget::get('navigation') ?>

		</div>
	</div>

	<!-- /HEADER -->


	<!-- CONTENT -->

	<div id="content" class="container-1">

		<div class="container-12 clearfix breadcrumb">
			<div class="grid-12">

<?= widget::get('breadcrumb') ?>

			</div>
		</div>

		<div class="container-12 clearfix">
			<div id="main-content" class="grid-10-full">

				<div id="title" class="grid-10">

<?= widget::get('actions') ?>

					<h2><?= $page_title ?></h2>
					<p class="subtitle"><?= $page_subtitle ?></p>
<?= widget::get('tabs') ?>
				</div>

				<div id="submenu" class="grid-10">

<?= widget::get('subnavigation') ?>

				</div>

<?= $content ?>

			</div>


			<!-- SIDE ADS -->

			<div id="right-ads" class="grid-2 side">

<?= widget::get('side_ads') ?>

			</div>

			<!-- /SIDE ADS -->

		</div>
	</div>

	<!-- /CONTENT -->


	<!-- DOCK -->

	<div id="dock" class="container-1">
		<div class="container-12 clearfix">
			<div class="grid-6 actions">

<?= widget::get('dock') ?>

			</div>
			<div class="grid-6 extra-actions">

<?= widget::get('dock2') ?>

			</div>
		</div>
	</div>

	<!-- /DOCK -->


	<!-- FOOTER -->

	<div id="footer" class="container-1">
		<div class="container-12 clearfix">

<?= widget::get('navigation') ?>
<?= widget::get('footer') ?>

		</div>
	</div>

	<!-- /FOOTER -->


	<!-- END -->

	<div id="end" class="container-1">
		<div class="container-12 clearfix">

<?= widget::get('end') ?>

		</div>
	</div>

	<!-- /END -->


<?= html::script(array('js/jquery.autocomplete.pack', 'js/jquery.hint')) ?>
<?= html::script_source("$('input:text,input:password,textarea').hint('hint');"); ?>

<script type="text/javascript">
/*
$(function() {
	var header = $('#header');
	header.css({ position: 'absolute' });

	var PADDING = 102;
	var FIXED = (header.css('position') == 'fixed');

	var win = $(window);

	win.scroll(function() {
		if (win.scrollTop() > PADDING) {
			if ($.browser.msie && $.browser.version == '6.0') {
				header.css('top', win.scrollTop() - PADDING);
			} else if (!FIXED) {
				//header.css({ top: -PADDING, position: 'fixed' });
				header.css({ top: -Math.min(win.scrollTop(), header.height()), position: 'fixed' });
				header.animate({ top: -PADDING }, { duration: 200, queue: false });
				FIXED = true;
			}
		} else {
			if (FIXED) {
				header.css({ top: 0, position: 'absolute' });
				FIXED = false;
			}
		}
	});
});
*/
</script>

<?= widget::get('foot') ?>

</body>
</html>
