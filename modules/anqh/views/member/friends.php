<ul class="contentlist friends">
	<?php foreach ($friends as $friend): ?>
	<li class="member clearfix">
		<?= html::avatar($friend->friend->avatar, $friend->friend->username) ?>
		<?= html::nick($friend->friend->id, $friend->friend->username) ?><br />
		<?= __('Last online :ago ago', array(
			':ago' => '<abbr title="' . date::format('DMYYYY_HM', $friend->friend->last_login) . '">' . date::timespan_short($friend->friend->last_login) . '</abbr>'
		)) ?>
	</li>
	<?php endforeach; ?>
</ul>
