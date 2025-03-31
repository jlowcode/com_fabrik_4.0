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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$uri = Uri::getInstance();

$d = $displayData;
$list = $d->list;
$pagination = $d->pagination;

$startClass = $list['start']['active'] == 1 ? ' active' : ' ';
$prevClass = $list['previous']['active'] == 1 ? ' active' : ' ';
$nextClass = $list['next']['active'] == 1 ? ' active' : ' ';
$endClass = $list['end']['active'] == 1 ? ' active' : ' ';

$id = $pagination->get('id');
$start = $uri->getVar('limitstart' . $id) ?? 1;
?>

<nav aria-label="Pagination">
	<ul class="pagination">
		<div class="d-flex">
			<li class="page-item<?php echo $startClass; ?>">
				<?php echo $list['start']['data']; ?>
			</li>
			<li class="page-item<?php echo $prevClass; ?>">
				<?php echo $list['previous']['data']; ?>
			</li>
		</div>

		<div class="d-flex">
			<div>
				<input type="text" name="nav-pagination" id="nav-pagination" class="nav-pagination" value="<?php echo ceil(($start+1)/$pagination->limit) ?>">
				/
				<?php echo $pagination->pagesTotal ?>
			</div>
			<button class="go-page" id="go-page"><?php echo Text::_("COM_FINDER_GO") ?></button>
			<input type="hidden" name="nav-pagination-url" value="<?php echo Route::_('limitstart' . $id . '=20'); ?>"></input>
			<input type="hidden" name="nav-pagination-limit" value="<?php echo $pagination->pagesTotal ?>"></input>
			<input type="hidden" name="nav-pagination-results-per-page" value="<?php echo $pagination->limit ?>"></input>
		</div>

		<div class="d-flex">
			<li class="page-item<?php echo $nextClass; ?>">
				<?php echo $list['next']['data'];?>
			</li>
			<li class="page-item<?php echo $endClass; ?>">
				<?php echo $list['end']['data'];?>
			</li>
		</div>
	</ul>
</nav>