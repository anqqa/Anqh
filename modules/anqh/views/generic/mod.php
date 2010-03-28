<?php
/**
 * View Mod
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="<?= $class ?>"<?= $id ? ' id="' . $id . '"' : '' ?>>
	<div class="container">

		<?php if ($title): ?>
		<header>
			<h4><?= $title ?></h4>
		</header>
		<?php endif; ?>

<?= $content ?>

	</div>
</section><!-- <?= $class ?> -->