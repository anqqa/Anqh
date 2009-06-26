<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $language ?>" lang="<?= $language ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= Kohana::config('site.site_name') ?></title>
	<link rel="icon" type="image/png" href="/ui/favicon.png" />
	<?= html::stylesheet(array('ui/boot', 'ui/grid', 'ui/typo', 'ui/invite')); ?> 
</head>

<body id="<?= $page_id ?>" class="<?= $page_class ?>">

	<div id="content" class="container-1">
		<div class="container-12">

			<div id="header" class="grid-4 prefix-4">
				<h1><?= Kohana::config('site.site_name') ?></h1>
				<h2><?= Kohana::lang('invite.intro') ?></h2>
			</div>
			
			<div id="login" class="grid-2 prefix-5">
				<?= form::open('sign/in') ?> 
				<fieldset>
					<?= form::label('username', Kohana::lang('member.username')) ?> 
					<?= form::input('username', null, 'title="' . Kohana::lang('member.username') . '"') ?> 
					
					<?= form::label('password', Kohana::lang('member.password')) ?> 
					<?= form::password('password', null, 'title="' . Kohana::lang('member.password') . '"') ?> 
					
					<?= form::submit('submit', Kohana::lang('member.signin')) ?> 
				</fieldset>
				<?= form::close() ?> 
				
			</div>
		
		</div>
	</div>
	
<?= widget::get('foot') ?> 

</body>
</html>
