<?php
/**
 * Layout: List Pagination Footer
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2015 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.3.3
 */

$d = $displayData;
$list = $d->list;

$startClass = $list['start']['active'] == 1 ? ' active' : ' ';
$prevClass = $list['previous']['active'] == 1 ? ' active' : ' ';
$nextClass = $list['next']['active'] == 1 ? ' active' : ' ';
$endClass = $list['end']['active'] == 1 ? ' active' : ' ';

?>

<nav aria-label="Pagination">
	<ul class="pagination">
		<li class="page-item<?php echo $startClass; ?>">
			<?php echo $list['start']['data']; ?>
		</li>
		<li class="page-item<?php echo $prevClass; ?>">
			<?php echo $list['previous']['data']; ?>
		</li>
		<?php
		foreach ($list['pages'] as $page) :
			$class = $page['active'] == 1 ? ' active' : ''; ?>
			<li class="page-item<?php echo $class; ?>">
				<?php echo $page['data']; ?>
			</li>
		<?php endforeach ;?>

		<li class="page-item<?php echo $nextClass; ?>">
			<?php echo $list['next']['data'];?>
		</li>
		<li class="page-item<?php echo $endClass; ?>">
			<?php echo $list['end']['data'];?>
		</li>
	</ul>
</nav>
