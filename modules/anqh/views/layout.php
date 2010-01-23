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
	<?= html::script(array(
		'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js',
		'js/jquery.tools.min.js',
	)) ?>
<?= widget::get('head') ?>
</head>

<body id="<?= $page_id ?>" class="<?= $page_width ?> <?= $page_main ?> <?= $page_class ?>">

	<!-- HEADER -->

	<header id="header">

		<div class="content header">

<h1><?= html::anchor('/', Kohana::config('site.site_name')) ?></h1>
<?= widget::get('header') ?>
<?= widget::get('navigation') ?>

		</div>
		<div class="content breadcrumb">
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

		<div class="content">


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
					<?= !empty($page_subtitle) ? '<p class="subtitle">' . $page_subtitle . '</p>' : '' ?>

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
		<div class="content">
			<div class="unit size2of5">

<?= widget::get('dock') ?>

			</div>
			<div class="unit size3of5 extra-actions">

<?= widget::get('dock2') ?>

			</div>
		</div>
	</div>

	<!-- /DOCK -->


	<!-- FOOTER -->

	<footer id="footer">
		<div class="content">

<?= widget::get('navigation') ?>
<?= widget::get('footer') ?>

		</div>
		<div id="end" class="content">

<?= widget::get('end') ?>

		</div>
	</footer>

	<!-- /FOOTER -->


<?= html::script(array('js/jquery.autocomplete.pack.js', 'js/jquery.form.js', 'js/jquery.text-overflow.js')) ?>

<script>
//<![CDATA[
$(function() {

	// Form input hints
	$('input:text, textarea, input:password').hint('hint');


	// Ellipsis ...
	$('.cut li').ellipsis();


	// Delete confirmations
	function confirm_delete(title, action) {
		if (title === undefined) title = '<?= __('Are you sure you want to do this?') ?>';
		if (action === undefined) action = function() { return true; }
		if ($('#dialog-confirm').length == 0) {
			$('body').append('<div id="dialog-confirm" title="' + title + '"><?= __('Are you sure?') ?></div>');
			$('#dialog-confirm').dialog({
				dialogClass: 'confirm-delete',
				modal: true,
				close: function(ev, ui) { $(this).remove(); },
				buttons: {
					'<?= __('Yes, do it!') ?>': function() { $(this).dialog('close'); action(); },
					'<?= __('No, cancel') ?>': function() { $(this).dialog('close'); }
				}
			});
		} else {
			$('#confirm-dialog').dialog('open');
		}
	}

	$('a[class*="-delete"]').live('click', function(e) {
		e.preventDefault();
		var action = $(this);
		if (action.data('action')) {
			confirm_delete(action.text(), function() { action.data('action')(); });
		} else if (action.is('a')) {
			confirm_delete(action.text(), function() { window.location = action.attr('href'); });
		} else {
			confirm_delete(action.text(), function() { action.parent('form').submit(); });
		}
	});

	$('.mod a[class*="-delete"]')
		.live('mouseenter', function () {
			$(this).closest('article').addClass('delete');
		})
		.live('mouseleave', function () {
			$(this).closest('article').removeClass('delete');
		});

	$('.mod a[class*="-edit"]')
		.live('mouseenter', function () {
			$(this).closest('article').addClass('edit');
		})
		.live('mouseleave', function () {
			$(this).closest('article').removeClass('edit');
		});


	// Peepbox
	if ($('#peepbox').length == 0) {
		$('body').append('<div id="peepbox"></div>');
		$('#peepbox').data('cache', []);
	}

	function peepbox(href, $tip) {
		var cache = $tip.data('cache');
		if (!cache[href]) {
			$tip.text('<?= __('Loading...') ?>');
			$.get(href + '?peep', function(response) {
				$tip.html(cache[href] = response);
			});
			$tip.data('cache', cache);
			return;
		}
		$tip.html(cache[href]);
	}

	$('a.user,.avatar a').tooltip({
		tip: '#peepbox',
		lazy: false,
		position: 'bottom right',
		onBeforeShow: function() {
			peepbox(this.getTrigger().attr('href'), this.getTip());
		}
	}).dynamic({
		borrom: {
			direction: 'down',
			bounce: true,
		}
	});

});
//]]>
</script>

<?= widget::get('foot') ?>

</body>

</html>
