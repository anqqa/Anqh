<ul class="contentlist memberlist">
	<?php $previous = false;
	      foreach ($users as $user):
	      	$day = date('YMD', strtotime($user->created));
	      	if ($previous != $day):
	      		if ($previous !== false):	?> 
		</ul>
	</li>
	<?php 		endif;			
	          $previous = $day;	?> 
	<li class="group clearfix">
		<?= html::box_day($user->created) ?>
		<ul class="prefix-1 members">
	<?php 	endif; ?> 
			<li class="member">
				<?= html::nick($user->id, $user->username) ?>
			</li>
	<?php endforeach; ?>
		</ul>
	</li>
</ul>
