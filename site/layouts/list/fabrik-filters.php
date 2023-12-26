<?php

/**
 * Layout: List filters
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.4
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$d             = $displayData;
$underHeadings = $d->filterMode === 3 || $d->filterMode === 4;
$clearFiltersClass = $d->gotOptionalFilters ? "clearFilters hasFilters" : "clearFilters";

$style = $d->toggleFilters ? 'style="display:none"' : ''; ?>
<div class="fabrikFilterContainer" <?php echo $style ?>>
	<?php
	if (!$underHeadings) :
	?>
		<?php
		if ($d->filterCols === 1) :
		?>
		<?php
		endif;
		?>
		<table class="filtertable table">
			<thead>
				<tr>
					<th style="border: none !important; display: flex">
						<?php if ($d->showClearFilters) : ?>
							<a style="display: block;" class="<?php echo $clearFiltersClass; ?>" href="#">
								<?php echo FText::_('COM_FABRIK_CLEAR') . ' ' . FabrikHelperHTML::icon('icon-refresh'); ?>
							</a>
						<?php endif ?>
					</th>
				</tr>
				<tr>
					<th style="border: none !important;">
						<div class="filteredTags">
							<span id="searchTag"></span>
						</div>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="1"></td>
				</tr>
			</tfoot>
			<?php
			$c = 0;
			// $$$ hugh - filterCols stuff isn't operation yet, WiP, just needed to get it committed
			if ($d->filterCols > 1) :
			?>
				<tr>
					<td colspan="1">
						<table class="filtertable_horiz">
							<?php
						endif;
						$filter_count = array_key_exists('all', $d->filters) ? count($d->filters) - 1 : count($d->filters);
						$colHeight    = ceil($filter_count / $d->filterCols);
						foreach ($d->filters as $key => $filter) :
							if ($d->filterCols > 1 && $c >= $colHeight && $c % $colHeight === 0) :
							?>
						</table>
						<table class="filtertable_horiz">
						<?php
							endif;
							if ($key !== 'all') :
								$c++;
								$required = $filter->required == 1 ? ' notempty' : ''; ?>
							<tr data-filter-row="<?php echo $key; ?>" class="fabrik_row oddRow<?php echo ($c % 2) . $required; ?>">
								<td style="font-weight: bold;">
									<?php if($filter->related_linked_list): ?>
										<a href="<?php echo $filter->related_linked_list;?>" target="_blank"><?php echo $filter->label; ?>:</a>
									<?php else:?>
										<a><?php echo $filter->label; ?>:</a>
									<?php endif;?>
									<?php 
										$user = JFactory::getUser();
										//If the user is an administrator
										if (isset($filter->popupform) && $filter->btnpopupform && (array_search(7, $user->groups) || array_search(8, $user->groups)) ):?>
											<div class="btn-group fabrik_action">
												<a class="dropdown-toggle btn btn-mini" data-toggle="dropdown" href="#">
													<span class="caret"></span>
												</a>
												<ul class="dropdown-menu">
													<li>
														<a id="tree-<?php echo $key; ?>_popupformbtn" formValue="<?php echo $filter->popupform; ?>" title="<?php echo FText::_('COM_FABRIK_ADD');?>" class="toggle-addoption btn-default" title="Adicionar">
														<?php echo FabrikHelperHTML::image('plus', 'form', 'teste', array('alt' => FText::_('COM_FABRIK_SELECT'))); ?>
														Adicionar
														</a>
													</li>
													<li>
														<a id="tree-<?php echo $key; ?>_refreshbutton" href="#" class="refreshTree btn-default" title="Atualizar árvore">
														<?php echo FabrikHelperHTML::image('refresh.png', 'form', 'teste', array('alt' => 'Atualizar árvore')); ?>
														Atualizar
														</a>
													</li>
												</ul>
											</div>
									<?php endif;?>
								</td>	
							</tr>
							<tr>
								<td><?php echo $filter->element; ?></td>
							</tr>
						<?php
							endif;
						endforeach;
						if ($d->filterCols > 1) :
						?>
						</table>
					</td>
				</tr>
			<?php
						endif;
			?>
		</table>
	<?php
	endif;
	?>
	<?php
	if (!($underHeadings)) :
	?>
		<?php
		if ($d->filterCols === 1) :
		?>
		<?php
		endif;
		?>
	<?php endif; ?>
</div>
