<?php
/**
 * Bootstrap grid layout
 *
 * NOTE - this layout must implode the grid with \n, as the calling func in HTML helper has an 'explode' arg,
 * which controls whether grid gets reutrned as string, or an array.
 */

defined('JPATH_BASE') or die;

$d = $displayData;

// avoid potential divide by 0 if something went wrong and $d->columns is 0 or empty
$span = empty($d->columns) ? 12 : floor(12 / $d->columns);
$i    = 0;
$id   = is_null($d->spanId) ? '' : ' id="' . $d->spanId . '"';
$grid = array();

foreach ($d->items as $i => $s)
{
	$grid[] = '<div class="' . $d->spanClass . ' span' . $span . '"' . $id . '>' . $s . '</div>';
}

echo implode("\n", $grid);
