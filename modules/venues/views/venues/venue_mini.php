
<?php if ($venue->default_image_id): ?>
<?= html::anchor(url::model($venue), html::img($venue->default_image, 'thumb'), array('style' => 'display:block;height:31px;')) ?>
<?php endif; ?>
<?= html::anchor(url::model($venue), $venue->name) ?><br />
<sup><?= $venue->venue_category->name ?></sup>
<?php if ($venue->address || $venue->city): ?>
<address>
	<?= html::specialchars($venue->address) ?><br />
	<?= html::specialchars($venue->zip) ?><br />
	<?= html::specialchars($venue->city_name) ?><br />
</address>
<?php endif; ?>
<?= html::anchor(url::model($venue), __('Show more'), array('class' => 'action')) ?>
