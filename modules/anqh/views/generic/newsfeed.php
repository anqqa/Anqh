<?php
/**
 * Newsfeed
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod newsfeed">
	<div>
		<ul>
			<?php foreach ($newsfeed as $item): ?>

				<li class="clearfix">
					<?= html::avatar($item['user']->avatar, $item['user']->username) ?>
					<?= html::user($item['user']) ?>
					<?= $item['text'] ?>
					<?= __(':ago ago', array(':ago' => html::time(date::timespan_short($item['stamp']), $item['stamp']))) ?>
				</li>
			<?php endforeach; ?>

		</ul>
	</div>
</section>
