<?php
// Get the day names
$days = Calendar::days(2);
array_unshift($days, Kohana::lang('generic.week_short'));

// Previous and next month timestamps
$prev = mktime(0, 0, 0, $month - 1, 1, $year);
$next = mktime(0, 0, 0, $month + 1, 1, $year);
$curr = mktime(0, 0, 0, $month, 1, $year);
$week_number = date('W', $curr);
$week_date = new DateTime();

// Previous and next month query URIs
$prev = '/events/' . date('Y/m', $prev);
$next = '/events/' . date('Y/m', $next);

$previous_padding = true;
$next_padding = false;
?>
<table class="calendar">
<thead>
	<tr>
		<td class="prev"><?= html::anchor($prev, '&laquo;') ?></td>
		<td class="month" colspan="6"><?= html::anchor('/calendar/' . date('Y/m', $curr), strftime('%B %Y', $curr)) ?></td>
		<td class="next"><?= html::anchor($next, '&raquo;') ?></td>
	</tr>
	<tr>
<?php foreach ($days as $day): ?>
	<th class="week"><?= $day ?></th>
<?php endforeach ?>
	</tr>
</thead>
<tbody>
<?php foreach ($weeks as $week):
if (empty($week)) continue;

// set week number
$previous_week = $week_number;
$week_date->setISODate($year, $week_number++, 4);

// check if week number looped to next year
//if ($week_date->format('W') < $previous_week) $week_date->setISODate($year + 1, 1);
//++$week_number; 
?>
	<tr>
		<th><?= html::anchor('/events/' . $week_date->format('Y') . '/week/' . $week_date->format('W'), $week_date->format('W')) ?></th>
<?php 	foreach ($week as $day):

list($number, $current, $data) = $day;
if (is_array($data)) {
	$classes = $data['classes'];
	$output = empty($data['output']) ? '' : '<ul class="output"><li>' . implode('</li><li>', $data['output']) . '</li></ul>';
} else {
	$classes = array();
	$output = '';
}
if (!$current) $classes[] = 'inactive';
$classes[] = 'day';

// check if we are padding days
$previous_padding = $previous_padding && !$current;
$next_padding = !$previous_padding && !$current;

// create current day
$date = mktime(0, 0, 0, $month - (int)$previous_padding + (int)$next_padding, $number, $year);

if (date('Y-n-j') == date('Y-n-j', $date)) $classes[] = 'today';
$link = '/events/' . date('Y/m/d', $date);
?>
		<td class="<?= implode(' ', $classes) ?>"><?= html::anchor($link, $number) ?><?= $output ?></td>
<?php 	endforeach ?>
	</tr>
<?php endforeach ?>
</tbody>
</table>
