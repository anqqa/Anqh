
<section class="mod blogentries">
	<?php foreach ($entries as $entry): ?>

		<article class="clearfix">
			<header>
				<?= html::avatar($entry->author->avatar, $entry->author->username) ?>
				<h4><?= html::anchor(url::model($entry), text::title($entry->name), array('title' => $entry->name)) ?></h4>
				<span class="details">
				<?= __('By :user :ago ago', array(
					':user'  => html::user($entry->author),
					':ago'   => html::time(date::timespan_short($entry->created), $entry->created)
				)) ?>
				</span>
			</header>

		</article>

	<?php endforeach; ?>
</section>
