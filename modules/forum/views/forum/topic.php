<?php
/**
 * Forum topic
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<?php foreach ($posts as $post):

	// Time difference between posts
	$current = strtotime($post->created);
	$difference = (isset($previous)) ? date::timespan($current, $previous, 'years,months') : array('years' => 0, 'months' => 0);
	if ($difference['years'] || $difference['months']):
?>

<div class="divider post-old"><?= __('Previous post over :ago ago', array(':ago' => date::timespan_short($current, $previous))) ?></div>

<?php endif;
	$previous = $current;

	echo View::factory('forum/post', array('topic' => $topic, 'post'  => $post, 'user'  => $user));

endforeach; ?>

<?php
echo html::script_source('
$(function() {
	$("a.post-edit").live("click", function(e) {
		e.preventDefault();
		var href = $(this).attr("href");
		var post = href.match(/([0-9]*)\\/edit/);
		$("#post-" + post[1] + " .actions").fadeOut();
		$.get(href, function(data) {
			$("#post-" + post[1] + " .post-content").html(data);
		});
	});

	$("a.post-delete").each(function(i) {
		var action = $(this);
		action.data("action", function() {
			var post = action.attr("href").match(/([0-9]*)\\/delete/);
			if (post) {
				$.get(action.attr("href"), function(data) {
					$("#post-" + post[1]).slideUp();
				});
			}
		});
	});

	$("a.post-quote").live("click", function(e) {
		e.preventDefault();
		var href = $(this).attr("href");
		var post = href.match(/([0-9]*)\\/quote/);
		var article = $(this).closest("article");
		$("#post-" + post[1] + " .actions").fadeOut();
		$.get(href, function(data) {
			article.append(data);
			window.scrollTo(0, article.find("#quote").offset().top - 20);
		});
	});

	$("section.post-content form").live("submit", function(e) {
		e.preventDefault();
		var post = $(this).closest("article");
		$.post($(this).attr("action"), $(this).serialize(), function(data) {
			post.replaceWith(data);
		});
	});

	$("section.post-content form a").live("click", function(e) {
		e.preventDefault();
		var post = $(this).closest("article");
		$.get($(this).attr("href"), function(data) {
			post.replaceWith(data);
		});
	});

	$("section#quote form a").live("click", function(e) {
		e.preventDefault();
		var section = $(this).closest("section");
		var article = section.closest("article");
		section.slideUp(null, function() { section.remove(); });
		article.find(".actions").fadeIn();
	});
});
');
