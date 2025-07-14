<?php
/**
 * Fabrik List Template: Div Row
 * Note the div cell container is now generated in the default template
 * in FabrikHelperHTML::bootstrapGrid();
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$rowClass = isset($this->_row->rowClass) ? $this->_row->rowClass : '';
$title_element_id = $this->params->get('titulo');
$regexTitle = $title_element_id. '_order';
$ignoreHeadings = ['fabrik_actions', 'fabrik_select'];
$rowData = $this->_row->data;
$elements = $this->getModel()->getElements('filtername');

?>
<div class="gallery-box d-flex flex-column justify-content-between h-100 <?php echo $rowClass; ?>">
	<div>
		<!-- Show thumb, name and description first -->
		<div class="gallery-head-row">
			<?php $el = $this->elementsToShowOnGridAndCardTemplate['thumb-gallery-card-mode']; ?>
			<?php if(isset($el)) : ?>
				<?php $el->reset(); ?>
				<div class="fabrikDivElement div-thumb">
					<span class="thumb <?php echo $el->getFullName() ?>" >
						<?php
							echo $el->getValue((array) @$rowData);
							$ignoreHeadings[] = $el->getFullName();
						?>
					</span>
				</div>
			<?php endif; ?>

			<?php $el = $this->elementsToShowOnGridAndCardTemplate['name-gallery-card-mode']; ?>
			<?php if(isset($el)) : ?>
				<?php $el->reset(); ?>
				<div class="fabrikDivElement div-name">
					<span class="title name <?php echo $el->getFullName() ?>">
						<?php
							echo $el->getValue((array) @$rowData);
							$ignoreHeadings[] = $el->getFullName();
						?>
					</span>
				</div>
			<?php endif; ?>
			
			<?php $el = $this->elementsToShowOnGridAndCardTemplate['description-gallery-card-mode']; ?>
			<?php if(isset($el)) : ?>
				<?php $el->reset(); ?>
				<div class="fabrikDivElement div-description">
					<span class="description">
						<p class="m-0 <?php echo $el->getFullName() ?>">
							<?php
								echo $el->getValue((array) @$rowData);
								$ignoreHeadings[] = $el->getFullName();
							?>
						</p>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<!-- Then show all data -->
		<div class="gallery-data-row">
			<?php foreach ($this->headings as $heading => $label) :
				$d = @$rowData->$heading;

				// Skip empty elements, id element, created_by element
				if (in_array(explode('___', $heading)[1], ["id"]) || in_array($heading, $ignoreHeadings)) continue;

				// If we use $label from $this->headings the label will be with a tag
				foreach ($elements as $element) {
					if($element->getFullName(true) == $heading) {
						$label = $element->getElement()->get('label');
					}
				}

				$h = $this->headingClass[$heading];
				$c = $this->cellClass[$heading];
				?>
				<div class="row-fluid fabrikDivElement fabrikDivElementData">
					<?php echo '<span class="muted title-field-card">' . $label . ': </span>'; ?>
					<?php echo '<span class="' . $c['class'] . '">' . $d . '</span>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- Finally show created_by, date_time and fabrik_actions -->
	<div class="fabrikDivElement info-row d-flex align-items-center justify-content-between">
		<div class="div-info d-flex flex-row flex-wrap">
			<?php $el = $this->elementsToShowOnGridAndCardTemplate['date-gallery-card-mode']; ?>
			<?php if (isset($el)) : ?>
				<?php $el->reset(); ?>
				<span class="date <?php echo $el->getFullName(); ?>">
					<?php
						$rawName = $el->getFullName() . '_raw';
						$allData = @$rowData;
						echo $el->renderListData(@$rowData->$rawName, $allData);
					?>
				</span>
			<?php endif; ?>

			<?php 
				$el = $this->elementsToShowOnGridAndCardTemplate['owner-gallery-card-mode']; 
			?>
			<?php if (isset($el)) : ?>
				<?php $el->reset(); ?>
				<span class="owner">
					<span><?php echo Text::_("COM_FABRIK_BY"); ?></span>
					<span class="<?php echo $el->getFullName(); ?>"><?php echo $el->getValue((array) @$rowData)[0] ?></span>
				</span>
			<?php endif; ?>
		</div>
		<div>
			<span><?php echo @$rowData->fabrik_actions; ?></span>
		</div>
	</div>
</div>