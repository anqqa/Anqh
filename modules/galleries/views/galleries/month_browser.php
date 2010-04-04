<?php
/**
 * Month browser
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<nav>
	<ol>

	<?php foreach ($months as $years => $y): ?>
		<li>
			<header><?= html::anchor('galleries/browse/' . $years, $years, array('class' => 'year' . ($year == $years ? ' selected' : ''))) ?></header>
			<ol>

			<?php foreach ($y as $m => $count): ?>
				<li><?= html::anchor('galleries/browse/' . $years . '/' . $m, $m, array('class' => 'month' . ($year == $years && $month == $m ? ' selected' : ''))) ?> (<?= $count ?>)</li>
			<?php endforeach ?>

			</ol>
		</li>
	<?php endforeach ?>

	</ol>
</nav>
