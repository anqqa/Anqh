<?php
/**
 * Edit blog entry
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
?>

<section class="mod entry-edit">
	<div>
		<?= form::open() ?>

		<fieldset>
			<ul>

				<?= form::input_wrap(array('name' => 'name', 'tabindex' => 1, 'maxlength' => 100, 'title' => __('Title')), $values, '', '', $errors) ?>

				<?= form::textarea_wrap(array('name' => 'entry', 'id' => 'entry', 'tabindex' => 2, 'rows' => 20, 'cols' => 25), $values, '', true, '', $errors) ?>

			</ul>
		</fieldset>

		<fieldset>
			<?= form::csrf() ?>
			<?= empty($values['id']) ? '' : form::hidden('id', $values['id']) ?>
			<?= form::submit(array('tabindex' => 3), __('Save')) ?>
			<?= html::anchor($_SESSION['history'], __('Cancel')) ?>
		</fieldset>

		<?= form::close() ?>
	</div>
</section>
<?php
echo html::script_source('$(function() { $("#entry").markItUp(bbCodeSettings); });');
