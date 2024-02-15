<?php

/**
 * Bootstrap List Template - Buttons
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

// Workflow code
if(isset($_REQUEST['workflow'])) {
	$this->showAddRequest = $_REQUEST['workflow']['showAddRequest'];
	$this->addRequestLink = $_REQUEST['workflow']['addRequestLink'];
	$this->requestLabel = $_REQUEST['workflow']['requestLabel'];
} else {
	$this->showAddRequest = null;
	$this->addRequestLink = null;
	$this->requestLabel = null;
}
// End workflow code

// Action code 
if(isset($_REQUEST['action']) && isset($_REQUEST['action']['showButton'])) {
	$this->showActionButton = $_REQUEST['action']['showButton'];
} else {
	$this->showActionButton = null;
}
// End action code 


?>
<div class="fabrikButtonsContainer row-fluid">
	<ul class="nav nav-pills pull-right">

		<?php 
		if ($this->showToggleCols) :
			echo $this->loadTemplate('togglecols');
		endif;

		//Verifies if shows layout dropdown in front-end
		if ($this->params->get('show-layout-selector') == '1') :
			$displayData = new stdClass;
			$displayData->icon = FabrikHelperHTML::icon('icon-list-view');
			$displayData->label = 'Layouts';
			$displayData->links = array();
			foreach ($this->layoutsHeadings as $url => $obj) :
				$classa = '';
				if($obj->layout == $_GET['layout'] || (!($_GET['layout']) && $obj->layout == 'bootstrap')){
					$classa = 'class="selected-atag"';
				}
				$displayData->links[] = '<a href="' . $url . '"' . $classa. '>' . $obj->label . '</a>';
			endforeach;

			$layout = $this->getModel()->getLayout('fabrik-nav-dropdown');
			echo $layout->render($displayData);
		?>
		<?php endif;

		if ($this->canGroupBy) :
			$displayData = new stdClass;
			$displayData->icon = FabrikHelperHTML::icon('icon-list-view');
			$displayData->label = Text::_('COM_FABRIK_GROUP_BY');
			$displayData->links = array();
			foreach ($this->groupByHeadings as $url => $obj) :
				$displayData->links[] = '<a data-groupby="' . $obj->group_by . '" href="' . $url . '">' . $obj->label . '</a>';
			endforeach;

			$layout = $this->getModel()->getLayout('fabrik-nav-dropdown');
			echo $layout->render($displayData);
		?>


		<?php endif;
		if (($this->showClearFilters && (($this->filterMode === 3 || $this->filterMode === 4))  || $this->bootShowFilters == false)) :
			$clearFiltersClass = $this->gotOptionalFilters ? "clearFilters hasFilters" : "clearFilters";
		?>
			<li>
				<a class="<?php echo $clearFiltersClass; ?>" href="#">
					<?php echo FabrikHelperHTML::icon('icon-refresh', Text::_('COM_FABRIK_CLEAR')); ?>
				</a>
			</li>
		<?php endif;
		if ($this->showFilters && $this->toggleFilters) : ?>
			<li>
				<?php if ($this->filterMode === 5) :
				?>
					<a href="#filter_modal" data-bs-toggle="modal">
						<?php echo $this->buttons->filter; ?>
						<span><?php echo Text::_('COM_FABRIK_FILTER'); ?></span>
					</a>
				<?php
				else :
				?>
					<a href="#" class="toggleFilters" data-filter-mode="<?php echo $this->filterMode; ?>">
						<?php echo $this->buttons->filter; ?>
						<span><?php echo Text::_('COM_FABRIK_FILTER'); ?></span>
					</a>
				<?php endif;
				?>
			</li>
		<?php endif;
		if ($this->advancedSearch !== '') : ?>
			<li>
				<a href="<?php echo $this->advancedSearchURL ?>" class="advanced-search-link">
					<?php echo FabrikHelperHTML::icon('icon-search', Text::_('COM_FABRIK_ADVANCED_SEARCH')); ?>
				</a>
			</li>
		<?php endif;
		echo $this->loadTemplate('csv_rss');
		// Workflow code
		echo $this->loadTemplate('event_button');
		// End workflow code
		?>
	<?php if (array_key_exists('all', $this->filters) || $this->filter_action != 'onchange') {
	?>
			<li>
				<div <?php echo $this->filter_action != 'onchange' ? 'class="input-append"' : ''; ?>>
					<?php if (array_key_exists('all', $this->filters)) {
						echo $this->filters['all']->element; ?>

					<?php };
					?>
				</div>
			</li>
		</ul>
	<?php
	}
	?>
</div>
