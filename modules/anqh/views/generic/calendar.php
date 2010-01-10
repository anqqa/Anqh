<?php
// Get the day names
$days = Calendar::days(2);
array_unshift($days, __('wk'));

// Previous and next month timestamps
$prev = mktime(0, 0, 0, $month - 1, 1, $year);
$next = mktime(0, 0, 0, $month + 1, 1, $year);
$curr = mktime(0, 0, 0, $month, 1, $year);

// Previous and next month query URIs
$prev = '/events/' . date('Y/m', $prev);
$next = '/events/' . date('Y/m', $next);

$previous_padding = true;
$next_padding = false;
?>
<section class="mod">
	<table class="calendar">
		<thead>
			<tr>
				<td class="prev"><?= html::anchor($prev, '&laquo;') ?></td>
				<td class="month" colspan="6"><?= html::anchor('events/' . date('Y/m', $curr), strftime('%B %Y', $curr)) ?></td>
				<td class="next"><?= html::anchor($next, '&raquo;') ?></td>
			</tr>
			<tr>
				<?php foreach ($days as $day): ?>
				<th class="week"><?= $day ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($weeks as $week): if (empty($week)) continue; $print_week = true; ?>

			<tr>
				<?php foreach ($week as $day):

				list($number, $current, $data) = $day;

				// Check if we are padding days
				$previous_padding = $previous_padding && !$current;
				$next_padding = !$previous_padding && !$current;

				// Create current day
				$date = mktime(0, 0, 0, $month - (int)$previous_padding + (int)$next_padding, $number, $year);

				// Print week number?
				if ($print_week) {
					$week_number = strftime(Kohana::config('locale.start_monday') ? '%V' : '%U', $date);
					$print_week = false;
				?>
				<th><?= html::anchor('events/' . date('Y', $date) . '/week/' . $week_number, $week_number) ?></th>
				<?php
				}

				if (is_array($data)) {
					$classes = $data['classes'];
					$output = empty($data['output']) ? '' : '<ul class="output"><li>' . implode('</li><li>', $data['output']) . '</li></ul>';
				} else {
					$classes = array();
					$output = '';
				}
				if (!$current) $classes[] = 'inactive';
				$classes[] = 'day';


				if (date('Y-n-j') == date('Y-n-j', $date)) $classes[] = 'today';
				$link = '/events/' . date('Y/m/d', $date);

				$week_number++;
				?>
				<td class="<?= implode(' ', $classes) ?>"><?= html::anchor($link, $number) ?><?= $output ?></td>
				<?php endforeach; ?>

			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>
