<?php
/**
 * Tabs
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section id="<?= $id ?>" class="mod tabs">
	<ul>
		<?php	$t = 0; $selected = 0; foreach ($tabs as $tab): $selected = !empty($tab['selected']) ? $t : $selected; $t++; ?>
		<li><?= html::anchor($tab['href'], $tab['title']) ?></li>
		<?php	endforeach; ?>
	</ul>
	<?php	foreach ($tabs as $tab) echo $tab['tab']; ?>
</section>
<?php
// Initialize tabs immediately to aviod ugly jumping
echo html::script_source('$("#' . $id . '").tabs({ selected: ' . $selected . ', collapsible: true, fx: { height: "toggle", opacity: "toggle", duration: "fast" } });');
//echo html::script_source('$("#' . $id . ' > ul").tabs("#' . $id . ' .tab", { initialIndex: ' . $selected . ', effect: "fade" });');
