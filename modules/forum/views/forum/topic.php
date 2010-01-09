
<!--<section class="mod topic topic-<?= $topic->id ?>">-->
	<?php foreach ($posts as $post):

		// Time difference between posts
		$current = strtotime($post->created);
		$difference = (isset($previous)) ? date::timespan($current, $previous, 'years,months') : array('years' => 0, 'months' => 0);
		if ($difference['years'] || $difference['months']):
	?>

	<div class="divider post-old"><?= __('Previous post over :ago ago', array(':ago' => date::timespan_short($current, $previous))) ?></div>

	<?php
		endif;
		$previous = $current;

		echo View::factory('forum/post', array(
			'topic' => $topic,
			'post'  => $post,
			'user'  => $user,
		));

	endforeach; ?>
<!--</section>-->
<?php

// AJAX hooks
echo html::script_source('
$(function() {
	$(".post-edit").click(function(e) {
		var post = $(this).attr("href").match(/([0-9]*)\\/edit/);
		if (post) {
			e.preventDefault();
			$.get($(this).attr("href"), function(data) {
				$("#post-" + post[1] + " .post-content").html(data);
			});
		}
	});

	$(".post-delete").each(function(i) {
		var action = $(this);
		action.data("action", function() {
			var post = action.attr("href").match(/([0-9]*)\\/delete/);
			if (post) {
				$.get(action.attr("href"), function(data) {
					$("#post-" + post[1]).fadeOut("slow");
				});
			}
		});
	});
});
');
