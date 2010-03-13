<?php
/**
 * Anqh pagination style
 *
 * @preview  « 1 2 3 … 50 … 100 »
 *
 * @package    Pagination
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Build page number array
if ($total_pages < 13):

	// Show all pages
	$pages = range(1, $total_pages);

else:

	// Add start and end
	$pages = array(1, $total_pages);
	if ($current_page < $total_pages / 2):
		$pages[] = 2;
		$pages[] = 3;
	else:
		$pages[] = $total_pages - 1;
		$pages[] = $total_pages - 2;
	endif;
	$pages = array_merge($pages, range(max(1, $current_page - 2), min($total_pages, $current_page + 2)));

	// Add halves if useful (min 5 pages from other segments)
	$first_half = ceil($current_page / 2);
	$last_half = ceil($total_pages - ($total_pages - $current_page) / 2);
	if ($first_half > 5 && $current_page - $first_half > 7) $pages[] = $first_half;
	if ($total_pages - $last_half > 5 && $last_half - $current_page > 7) $pages[] = $last_half;
endif;
sort($pages);
$pages = array_unique($pages);
$previous = 1;
?>

<p class="pagination">

	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&laquo;&nbsp;</a>
	<?php else: ?>
		&laquo;&nbsp;
	<?php endif ?>

	<?php foreach ($pages as $page): ?>
		<?php if ($page - $previous > 1): ?>
			&hellip;
		<?php endif; $previous = $page; ?>
		<?php if ($page == $current_page): ?>
			<strong><?php echo $page ?></strong>
		<?php else: ?>
			<a href="<?php echo str_replace('{page}', $page, $url) ?>"><?php echo $page ?></a>
		<?php endif ?>
	<?php endforeach ?>

	<?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>">&nbsp;&raquo;</a>
	<?php else: ?>
		&nbsp;&raquo;
	<?php endif ?>

</p>