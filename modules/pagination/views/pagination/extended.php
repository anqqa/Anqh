<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Extended pagination style
 *
 * @preview  « Previous | Page 2 of 11 | Showing items 6-10 of 52 | Next »
 */
?>

<p class="pagination">

	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&laquo;&nbsp;<?php echo __('Previous') ?></a>
	<?php else: ?>
		&laquo;&nbsp;<?php echo __('Previous') ?>
	<?php endif ?>

	| <?php echo __('Page') ?> <?php echo $current_page ?> <?php echo __('of') ?> <?php echo $total_pages ?>

	| <?php echo __('items') ?> <?php echo $current_first_item ?>&ndash;<?php echo $current_last_item ?> <?php echo __('of') ?> <?php echo $total_items ?>

	| <?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>"><?php echo __('Next') ?>&nbsp;&raquo;</a>
	<?php else: ?>
		<?php echo __('Next') ?>&nbsp;&raquo;
	<?php endif ?>

</p>