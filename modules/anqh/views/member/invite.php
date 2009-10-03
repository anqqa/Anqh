
<?= form::open('sign/up') ?>

	<?= html::box_step(1) ?>
	<fieldset class="prefix-1">
		<legend><?= __('Not yet invited?') ?></legend>
		<p><?= __("No problem! If you don't already have an invitation, get one here or invite your friend.") ?></p>
		<?php if (empty($message)): ?>
		<ul>
			<?= form::input_wrap('email', $values, 'title="' . __('john.doe@domain.tld') . '" maxlength="127"', __('Send an invitation to'), $errors) ?>
			<li class="tip"><?= __('Please remember sign up is available only with a valid, invited email.') ?></li>
			<li><?= form::submit('invitation_send', __('Send invitation')) ?></li>
		</ul>
		<?php else:?>
		<p class="message"><?= $message ?></p>
		<?php endif; ?>
	</fieldset>

<?= form::close() ?>


<?= form::open('sign/up') ?>

	<?= html::box_step(2) ?>
	<fieldset class="prefix-1">
		<legend><?= __('Got my invitation!') ?></legend>
		<p><?= __('You received your invitation and are ready to sign up? Excellent! ') ?></p>
		<ul>
			<?= form::input_wrap('code', $values, 'title="' . __('M0573XC3LL3N751R') . '" maxlength="32"', __('Enter your invitation code'), $errors) ?>
			<li class="tip"><?= __('Your invitation code is included in the mail you received, 16 characters.') ?></li>
			<li><?= form::submit('invitation_check', __('Final step!')) ?></li>
		</ul>
	</fieldset>

<?= form::close() ?>
