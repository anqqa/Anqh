<?php
/**
 * Forum post
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Viewer's post
$my = ($user && $post->author_id == $user->id);

// Topic author's post
$owners = ($topic->author_id && $post->author_id == $topic->author_id);
?>

	<article id="post-<?= $post->id ?>" class="post <?= ($owners ? 'owner ' : '') . ($my ? 'my ' : '') . text::alternate('', 'alt') ?>">
		<header<?= $post->id == $topic->last_post_id ? ' id="last"' : '' ?>>

			<?= html::avatar($post->author->avatar, $post->author->username) ?>

			<span class="actions">
			<?php if ($my): ?>

				<?= html::anchor('forum/post/' . $post->id . '/edit', __('Edit'), array('class' => 'action post-edit')) ?>
				<?= html::anchor('forum/post/' . $post->id . '/delete/?token=' . csrf::token(), __('Delete'), array('class' => 'action post-delete')) ?>

			<?php endif; ?>
			<?php if ($topic->has_access(Forum_Topic_Model::ACCESS_WRITE)): ?>

				<?= html::anchor('forum/post/' . $post->id . '/quote', __('Quote'), array('class' => 'action post-quote')) ?>

			<?php endif; ?>
			</span>

			<span class="details">
			<?= __(':user, :ago ago',
				array(
					':user' => html::user($post->author_id, $post->author_name),
					':ago'  => html::time(date::timespan_short($post->created), $post->created)
				)) ?>
			<?php if ($post->modifies > 0): ?>
			<br />
			<?= __('Edited :ago ago',
				array(
					':ago' => html::time(date::timespan_short($post->modified), $post->modified)
				)) ?>
			<?php endif; ?>
			<?php if ($post->parent_id): $parent_topic = $post->parent->forum_topic; ?>
			<br />
			<?= __('Replying to :parent',
				array(
					':parent' => html::anchor(url::model($parent_topic) . '/' . $post->parent_id . '#post-' . $post->parent_id, text::title($parent_topic->name)),
				)) ?>
			<?php endif; ?>
			</span>

		</header>

		<section class="post-content">

<?= BB::factory($post->post)->render() ?>

		</section>
	</article>
