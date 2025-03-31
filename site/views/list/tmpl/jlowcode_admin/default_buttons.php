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

if($this->toggleFilters) {
	Text::script('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS');
	Text::script('JGLOBAL_SELECT_AN_OPTION');
	Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
}

$listModel = $this->_models["list"];
$elsList = $listModel->getElements('id');
$tree = false;

foreach ($elsList as $el) {
    $params = $el->getParams();
    if (
        str_contains($el->getName(), 'Databasejoin') && $params->get('database_join_display_type') == 'auto-complete'
        && $params->get('join_db_name') == $listModel->getTable()->get('db_table_name') &&
        ($params->get('database_join_display_style') == 'both-treeview-autocomplete' || $params->get('database_join_display_style') == 'only-treeview')
    ) {
        $tree = true;
    }
}

?>
<div class="fabrikButtonsContainer d-flex">
	<div class="filter-input">
		<?php if (array_key_exists('all', $this->filters) || $this->filter_action != 'onchange') : ?>
			<div <?php echo $this->filter_action != 'onchange' ? 'class="input-append"' : ''; ?>>
				<?php
					if (array_key_exists('all', $this->filters)) :
						echo $this->filters['all']->element;
					endif;
				?>
			</div>
		<?php endif; ?>
	</div>
	
	<div class="middle-buttons d-flex">
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
			endif;

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
			endif;
		?>
			
		<?php 
			if (($this->showClearFilters && (($this->filterMode === 3 || $this->filterMode === 4))  || $this->bootShowFilters == false)) :
				$clearFiltersClass = $this->gotOptionalFilters ? "clearFilters hasFilters" : "clearFilters";
		?>
				<li>
					<a class="<?php echo $clearFiltersClass; ?>" href="#">
						<?php echo FabrikHelperHTML::icon('icon-refresh', Text::_('COM_FABRIK_CLEAR')); ?>
					</a>
				</li>
		<?php endif; ?>

		<?php if ($this->showFilters && $this->toggleFilters) : ?>
			<li>
				<?php if ($this->filterMode === 5) :
				?>
					<a href="#filter_modal" data-bs-toggle="modal">
						<?php echo $this->buttons->filter; ?>
						<span><?php echo Text::_('COM_FABRIK_FILTER'); ?></span>
					</a>
				<?php else : ?>
					<a href="#" class="toggleFilters" data-filter-mode="<?php echo $this->filterMode; ?>">
						<?php echo $this->buttons->filter; ?>
						<span><?php echo Text::_('COM_FABRIK_FILTER'); ?></span>
						<span class="num-button"><?php echo count($this->filters)-1?></span>
					</a>
				<?php endif; ?>
			</li>
		<?php endif; ?>

		<?php if ($this->advancedSearch !== '') : ?>
			<li>
				<a href="<?php echo $this->advancedSearchURL ?>" class="advanced-search-link">
					<?php echo FabrikHelperHTML::icon('icon-search', Text::_('COM_FABRIK_ADVANCED_SEARCH')); ?>
				</a>
			</li>
		<?php endif; ?>
		
		<!-- Begin - Css Buttons -->
		<?php if ($this->showCSV) :
			if ($this->showCSVImport) :?>
				<li>
					<a href="<?php echo $this->csvImportLink ?>" class="csvImportButton"> 
						<?php echo $this->buttons->csvimport ?>
						<span><?php echo Text::_('COM_FABRIK_IMPORT_FROM_CSV') ?></span>
					</a>
				</li>
			<?php endif;
			if ($this->showCSV) : ?>
				<li>
					<a href="#" class="csvExportButton">
						<?php echo $this->buttons->csvexport ?>
						<span><?php echo Text::_('COM_FABRIK_EXPORT_TO_CSV') ?></span>
					</a>
				</li>
			<?php endif;
			if ($this->showRSS) : ?>
				<li>
					<a href="<?php echo $this->rssLink; ?>" class="feedButton">
						<?php echo FabrikHelperHTML::image('feed.png', 'list', $this->tmpl); ?>
						<?php echo Text::_('COM_FABRIK_SUBSCRIBE_RSS'); ?>
					</a>
				</li>
			<?php endif;
			if ($this->showPDF) : ?>
				<li><a href="<?php echo $this->pdfLink; ?>" class="pdfButton">
						<?php echo FabrikHelperHTML::icon('icon-file', Text::_('COM_FABRIK_PDF')); ?>
					</a></li>
			<?php endif;
			if ($this->emptyLink) : ?>
				<li>
					<a href="<?php echo $this->emptyLink ?>" class="doempty">
						<?php echo $this->buttons->empty; ?>
						<?php echo Text::_('COM_FABRIK_EMPTY') ?>
					</a>
				</li>
			<?php endif ?>
		<?php endif ?>
		<!-- End - Css Buttons -->

		<!-- Begin - Sub render Buttons -->
		<?php
			$links = Array();
			$links[] = '<a id="list-view" name="view" onclick="handleRadioClick(this)">' . FabrikHelperHTML::image('list-2.png', 'list', $this->tmpl) . Text::_("COM_FABRIK_LAYOUT_MODE_OPTION_0") . '</a>';
			$links[] = '<a id="grid-view" name="view" onclick="handleRadioClick(this)">' . FabrikHelperHTML::image('grid.png', 'list', $this->tmpl) . Text::_("COM_FABRIK_LAYOUT_MODE_OPTION_1") . '</a>';

			if ($tree == true) {
				$links[] = '<a id="tree-view" name="view" onclick="handleRadioClick(this)">' . FabrikHelperHTML::image('hierarchy.png', 'list', $this->tmpl) . Text::_("COM_FABRIK_LAYOUT_MODE_OPTION_2") . '</a>';
			}

			if($this->canShowTutorialTemplate == true) {
				$links[] = '<a id="tutorial-view" name="view" onclick="handleRadioClick(this)">' . FabrikHelperHTML::image('notification.png', 'list', $this->tmpl) . Text::_("COM_FABRIK_LAYOUT_MODE_OPTION_3") . '</a>';
			}

			$displayData = new stdClass;
			$displayData->icon = FabrikHelperHTML::image('list-2.png');
			$displayData->label = Text::_("COM_FABRIK_LAYOUT_MODE");
			$displayData->links = $links;

			$layout = $this->getModel()->getLayout('fabrik-nav-dropdown');
			echo $layout->render($displayData);
		?>
		<!-- End - Sub render Buttons -->

		<?php 
			if ($_REQUEST['workflow']['showEventsButton'] == true) :
				$url = ($_REQUEST['wfl_action'] == 'list_requests') ? $_REQUEST['workflow']['list_link'] : $_REQUEST['workflow']['requests_link'];
				$active = ($_REQUEST['wfl_action'] == 'list_requests') ? 'active' : '';
		?>
			<li class="<?php echo $active ?>">
				<a id="showRequests" class="showRequests" href="<?php echo $url ?>">
					<?php echo $_REQUEST['workflow']['eventsButton'] ?>
					<span class="num-button"><?php echo $_REQUEST['workflow']['requests_count'] ?></span>
				</a>
			</li>
		<?php endif; ?>
	</div>

	<div class="header_buttons d-flex">
		<?php if ($this->showAdd) : ?>
			<li>
				<a class="addbutton" href="<?php echo $this->addRecordLink; ?>">
					<?php echo FabrikHelperHTML::icon('icon-plus', $this->addLabel); ?>
				</a>
			</li>
		<?php endif; ?>

		<!-- Begin - Workflow code -->
		<?php if ($this->showAddRequest) : ?>
			<li>
				<a class="addbutton" href="<?php echo $this->addRequestLink;?>">
					<?php echo FabrikHelperHTML::icon('icon-plus', $this->addLabel);?>
				</a>
			</li>
		<?php endif; ?>
		<!-- End - Workflow code -->

		<!-- Begin - Action code -->
		<?php if ($this->showActionButton) : ?>
			<li>
				<a class="addbutton actionButton"></a>
			</li>
		<?php endif; ?>
		<!-- End action code -->
	</div>
</div>
