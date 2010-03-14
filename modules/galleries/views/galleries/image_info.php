<?php
/**
 * Image info
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod gallery-image-info">
	<div>
		<dl>

			<dt><?= __('Details') ?></dt>
			<?php if ($image->author_id): ?>
				<dd><?= __('Copyright &copy; :year :author', array(':year'   => date('Y', strtotime($image->created)), ':author' => html::user($image->author))) ?></dd>
			<?php endif ?>
				<dd><?= __('Added: :date', array(':date' => '<var>' . date::format('DMYYYY_HM', $image->created) . '</var>')) ?></dd>
				<dd><?= __('Image size: :size kB', array(':size' => '<var>' . text::bytes($image->original_size, 'KiB', '%01.2f') . '</var>')) ?></dd>
				<dd><?= __('Resolution: :resolution', array(':resolution' => sprintf('<var>%d×%d</var>', $image->original_width, $image->original_height))) ?></dd>

			<dt><?= __('Statistics') ?></dt>
				<dd><?= __('Comments: :comments', array(':comments' => '<var>' . $image->comments . '</var>')) ?></dd>
				<dd><?= __2('Score: :score (:votes vote)', 'Score: :score (:votes votes)', $image->votes, array(':score' => '<var>' . $image->score . '</var>', ':votes' => '<var>' . $image->votes . '</var>')) ?></dd>
				<dd><?= __('Views: :views', array(':views' => '<var>' . $image->views . '</var>')) ?></dd>

			<?php if ($image->exif): ?>
			<dt><?= __('Camera') ?></dt>
				<?php if ($image->exif->make): ?>
				<dd><?= __('Make: :make', array(':make' => '<var>' . text::title($image->exif->make, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->model): ?>
				<dd><?= __('Model: :model', array(':model' => '<var>' . text::title($image->exif->model, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->exposure): ?>
				<dd><?= __('Shutter speed: :exposure second', array(':exposure' => '<var>' . text::title($image->exif->exposure, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->aperture): ?>
				<dd><?= __('Aperture: F/:aperture', array(':aperture' => '<var>' . text::title($image->exif->aperture, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->focal): ?>
				<dd><?= __('Focal length: :focal mm', array(':focal' => '<var>' . text::title($image->exif->focal, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->iso): ?>
				<dd><?= __('ISO speed: :iso', array(':iso' => '<var>' . text::title($image->exif->iso, true))) ?></dd>
				<?php endif ?>
				<?php if ($image->exif->taken): ?>
				<dd><?= __('Taken: :taken', array(':taken' => '<var>' . date::format('DMYYYY_HM', $image->exif->taken))) ?></dd>
				<?php endif ?>
			<?php endif ?>

		</dl>
	</div>
</section>
