<ul class="contentlist invitation">

	<li class="group">
		<?= form::open() ?>
		<?= html::box_step(1) ?>
		<h3><?= Kohana::lang('member.invitation_require') ?></h3>
		<p class="prefix-1">
			<?= Kohana::lang('member.invitation_intro') ?>
		</p>
		
		<?php if (empty($message)): ?>
		<p class="prefix-1<?= isset($errors['email']) ? ' error' : '' ?>">
			<?= html::error($errors, 'email') ?>
			<?= form::label('email', Kohana::lang('member.invitation_send_to')) ?>
			<?= form::input('email', $values['email'], 'title="' . Kohana::lang('member.email_example') . '" maxlength="127"') ?>
			<span class="tip"><?= Kohana::lang('member.tip_invitation') ?></span>
		</p>
		
		<p class="prefix-1">
			<span class="buttons">
				<?= form::submit('invitation_send', Kohana::lang('member.invitation_send')) ?>
			</span>
		</p>
		<?php else:?>
		<p class="prefix-1">
			<span class="message"><?= $message ?></span>
		</p>
		<?php endif; ?>
		
		<?= form::close() ?>
	</li>

	<li class="group">
		<?= form::open() ?>
		<?= html::box_step(2) ?>
		<h3><?= Kohana::lang('member.invitation_received') ?></h3>
		<p class="prefix-1">
			<?= Kohana::lang('member.invitation_ready') ?>
		</p>
		
		<p class="prefix-1<?= isset($errors['code']) ? ' error' : '' ?>">
			<?= html::error($errors, 'code') ?>
			<?= form::label('code', Kohana::lang('member.invitation_code')) ?>
			<?= form::input('code', $values['email'], 'title="' . Kohana::lang('member.invitation_example') . '" maxlength="32"') ?>
			<span class="tip"><?= Kohana::lang('member.tip_invitation_code') ?></span>
		</p>
		
		<p class="prefix-1">
			<span class="buttons">
				<?= form::submit('invitation_check', Kohana::lang('member.signup')) ?>
			</span>
		</p>
		<?= form::close() ?>
	</li>

</ul>
