<?php
/**
 * Blog entry
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod blogentry">
	<div>
		<article>

<?= BB::factory($entry->entry)->render() ?>

		</article>
	</div>
</section>
