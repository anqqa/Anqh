<?php
/**
 * Forum post edit
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

$post_id = (empty($post['id']) ? 'post-new-content' : 'post-' . $post['id'] . '-content');
?>

<section class="mod post-edit">
	<div>
		<?= form::open() ?>

		<fieldset>
			<?php if (!request::is_ajax()): ?><legend><h3><?= __('Post') ?></h3></legend><?php endif; ?>
			<ul>

				<?= form::textarea_wrap(array('name' => 'post', 'id' => $post_id, 'rows' => 20, 'cols' => 25), $post, '', true, '', $errors) ?>

			</ul>
		</fieldset>

		<fieldset>
			<?= form::csrf() ?>
			<?= empty($post['id']) ? '' : form::hidden('id', $post['id']) ?>
			<?= empty($parent_id) ? '' : form::hidden('parent_id', $parent_id) ?>
			<?= form::submit(false, __('Save')) ?>
			<?= html::anchor(request::is_ajax() ? 'forum/post/' . $post['id'] : $_SESSION['history'], __('Cancel')) ?>
		</fieldset>

		<?= form::close() ?>
	</div>
</section>
<?php
echo html::script_source('$(function() { $("#' . $post_id . '").markItUp(bbCodeSettings); });');
