<?php
/**
 * Shouts
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<ul>
<?php foreach ($shouts as $shout): ?>

	<li><?= html::time(date::format('HHMM', $shout->created), $shout->created) ?> <?= html::user($shout->author, $shout->author_name) ?>: <?= html::chars($shout->message) ?></li>

<?php endforeach; ?>
</ul>
