<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
google.load("maps", "2.x", {"language" : "<?php echo substr(Kohana::config('locale.language.0'), 0, 2);?>"});
var gmap_is_open = false;

function gmap_open() {

	if (!gmap_is_open && GBrowserIsCompatible()) {
		gmap_is_open = true;
	
		// Initialize the GMap
		<?= $map ?> 
		<?= $controls ?> 
		<?= $center ?> 
		<?= $options->render(1) ?> 
		
		<?php if (!empty($icons)): ?>
		// Build custom marker icons
			<?php foreach($icons as $icon): ?>
				<?= $icon->render(1) ?> 
			<?php endforeach ?>
		<?php endif ?>

		// Show map points
		<?php foreach($markers as $marker): ?>
			<?= $marker->render(1) ?> 
		<?php endforeach ?>
		
	}
	
}

// Unload the map when the window is closed
$(document.body).unload(function() { GBrowserIsCompatible() && GUnload(); });
