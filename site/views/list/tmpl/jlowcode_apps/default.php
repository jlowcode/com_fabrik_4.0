<?php
/**
 * Fabrik List Template: Div
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

// No direct access
defined('_JEXEC') or die('Restricted access');

// The number of columns to split the list rows into
$columns = 4;

// Show the labels next to the data:
$this->showLabels = false;

// Show empty data
$this->showEmpty = true;


$pageClass = $this->params->get('pageclass_sfx', '');

if ($pageClass !== '') :
	echo '<div class="' . $pageClass . '">';
endif;

?>
<?php if ($this->tablePicker != '') { ?>
	<div style="text-align:right"><?php echo Text::_('COM_FABRIK_LIST') ?>: <?php echo $this->tablePicker; ?></div>
<?php }

if ($this->params->get('show_page_heading')) :
	echo '<h1>' . $this->params->get('page_heading') . '</h1>';
endif;

if ($this->showTitle == 1) { ?>
	<h1><?php echo $this->table->label; ?></h1>
<?php } ?>

<?php echo $this->table->intro; ?>
<form class="fabrikForm" action="<?php echo $this->table->action;?>" method="post" id="<?php echo $this->formid;?>" name="fabrikList">
	<div class="<?php echo $this->params['show-table-filters'] === '6' ? 'row' : ''; ?>">
		<div class="<?php echo $this->params['show-table-filters'] === '6' ? 'col-md-12' : ''; ?>">
			<?php
			if ($this->hasButtons) :
				echo $this->loadTemplate('buttons');
			endif;
			?>
		</div>


		<div class="fabrikDataContainer<?php echo $this->params['show-table-filters'] === '6' ? ' col-md-10 span9' : ''; ?>" data-cols="<?php echo $columns; ?>">

		<?php foreach ($this->pluginBeforeList as $c) {
			echo $c;
		}?>
		<div class="fabrikList" id="list_<?php echo $this->table->renderid;?>" >
		
			<?php
			$gCounter = 0;
			foreach ($this->rows as $groupedBy => $group) :?>
			<?php
			if ($this->isGrouped) :
				$imgProps = array('alt' => Text::_('COM_FABRIK_TOGGLE'), 'data-role' => 'toggle', 'data-expand-icon' => 'fa fa-arrow-down', 'data-collapse-icon' => 'fa fa-arrow-right');
			?>
			<div class="fabrik_groupheading">
				<?php echo $this->layoutGroupHeading($groupedBy, $group); ?>
			</div>
			<?php
			endif;
			?>
			<div class="fabrik_groupdata">
				<div class="groupDataMsg">
					<div class="emptyDataMessage" style="<?php echo $this->emptyStyle?>">
						<?php echo $this->emptyDataMessage; ?>
					</div>
				</div>

			<?php

			$items = array();
			foreach ($group as $this->_row) :
				$items[] = $this->loadTemplate('row');
			endforeach;
			$class = 'fadeIn fabrik_row well col-md-4 galery-div';
			echo FabrikHelperHTML::bootstrapGrid($items, $columns, $class, true, $this->_row->id);
			?>
			</div>
			<?php
			endforeach;
		?>

		</div>
		<?php
		echo $this->nav;
		print_r($this->hiddenFields);?>
		</div>
	</div>
</form>
<?php
echo $this->table->outro;

if ($pageClass !== '') :
	echo '</div>';
endif;
?>