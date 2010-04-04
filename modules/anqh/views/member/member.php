<?php
/**
 * Member pictures
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php if (valid::url($user->picture)): ?>
<?= html::image($user->picture); ?>
<?php endif; ?>

<?php if ($user->default_image_id): ?>
<?= html::img($user->default_image) ?>
<?php endif; ?>
