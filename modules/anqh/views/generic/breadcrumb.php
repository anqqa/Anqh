<div class="nav"><!-- <nav> -->
<?php $last = array_pop($breadcrumb); array_push($breadcrumb, '<strong>' . $last . '</strong>'); ?>
<?= implode(" &raquo; \n", $breadcrumb) ?>
</div><!-- </nav> -->