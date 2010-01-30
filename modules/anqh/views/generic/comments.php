
<section class="mod comments">

	<header>
		<h3><?= __('Comments') ?></h3>

		<?= $pagination ?>

	</header>

	<?= form::open() ?>
	<fieldset class="horizontal">
		<ul>
			<?php if ($private): ?>
			<?= form::checkbox_wrap('private', '1', $values, "onchange=\"$('input[name=comment]').toggleClass('private', this.checked)\"", '<abbr class="private" title="' . __('Private comment') . '">' . __('Priv') . '</abbr>') ?>
			<?php endif; ?>

			<?= form::input_wrap(array('name' => 'comment', 'id' => 'comment', 'maxlength' => 300), '', '', '', $errors) ?>

			<li><?= form::submit(false, __('Comment')) ?></li>
		</ul>
		<?= form::csrf() ?>
	</fieldset>
	<?= form::close() ?>

	<?php foreach ($comments as $comment):
		$classes = array();

		if ($comment->private) {
			$classes[] = 'private';
		}

		// Viewer's post
		if ($user && $comment->author_id == $user->id) {
			$classes[] = 'my';
			$mine = true;
		} else {
			$mine = false;
		}

		// Topic author's post
		if ($comment->author_id == $comment->user_id) {
			$classes[] = 'owner';
		}
 	?>

	<article id="comment-<?= $comment->id ?>" class="<?= implode(' ', $classes) ?>">

		<?= html::avatar($comment->author->avatar, $comment->author->username) ?>

		<header>
			<?php if ($user && $comment->user_id == $user->id || $mine): ?>
			<span class="actions">
				<?php if ($private && !$comment->private): ?>
				<?= html::anchor(sprintf($private, $comment->id), __('Set as private'), array('class' => 'action comment-private')) ?>
				<?php endif; ?>
				<?= html::anchor(sprintf($delete, $comment->id), __('Delete'), array('class' => 'action comment-delete')) ?>
			</span>
			<?php endif; ?>

			<?= html::nick($comment->author_id, $comment->author->username) ?>,
			<?= __(':ago ago', array(
				':ago' => html::time(date::timespan_short($comment->created), $comment->created))
			) ?>
		</header>

		<p>
			<?= $comment->private ? '<abbr title="' . __('Private comment') . '">' . __('Priv') . '</abbr>: ' : '' ?>
			<?= text::smileys(text::auto_link_urls(html::specialchars($comment->comment))) ?>
		</p>
	</article>

	<?php endforeach; ?>

	<footer>

		<?= $pagination ?>

	</footer>

</section>
<?php

// AJAX hooks
echo html::script_source('
$(function() {

	$("a.comment-delete").each(function(i) {
		var action = $(this);
		action.data("action", function() {
			var comment = action.attr("href").match(/([0-9]*)\\/delete/);
			if (comment) {
				$.get(action.attr("href"), function() {
					$("#comment-" + comment[1]).slideUp();
				});
			}
		});
	});

	$("a.comment-private").live("click", function(e) {
		e.preventDefault();
		var href = $(this).attr("href");
		var comment = href.match(/([0-9]*)\\/private/);
		$(this).fadeOut()
		if (comment) {
			$.get(href, function() {
				$("#comment-" + comment[1]).addClass("private");
			});
		}
		return false;
	});

	$("section.comments form").live("submit", function(e) {
		e.preventDefault();
		var comment = $(this).closest("section.comments");
		$.post($(this).attr("action"), $(this).serialize(), function(data) {
			comment.replaceWith(data);
		});
		return false;
	});

});
');
