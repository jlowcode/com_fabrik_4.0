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

$elDate = $this->elementsToShowOnGridAndCardTemplate['date-gallery-card-mode'];
if(isset($elDate)) {
	$elDate->reset();
	$dateFullName = $elDate->getFullName();
	$ignoreHeadings[] = $dateFullName;
	$dateRawName = $dateFullName . '_raw';
}

$elOwner = $this->elementsToShowOnGridAndCardTemplate['owner-gallery-card-mode'];
if(isset($elOwner)) {
	$elOwner->reset();
	$ownerFullName = $elOwner->getFullName();
	$ignoreHeadings[] = $ownerFullName;
}
?>
<div class="fabrik_row card-box d-flex flex-row justify-content-between w-100 <?php echo $rowClass; ?>" id="<?php echo $this->_row->id; ?>">
	<div class="d-flex flex-row w-100">
		<!-- Show thumb, name and description first -->
		<?php $el = $this->elementsToShowOnGridAndCardTemplate['thumb-gallery-card-mode']; ?>
		<?php if(isset($el)) : ?>
			<?php $el->reset(); ?>
			<div class="fabrikDivElement div-thumb">
				<span class="thumb <?php echo $el->getFullName() ?>">
					<?php
						echo $el->getValue((array) @$rowData);
						$ignoreHeadings[] = $el->getFullName();
					?>
				</span>
			</div>
		<?php endif; ?>

		<div class="card-head-row w-100">
			<?php
				$el = $this->elementsToShowOnGridAndCardTemplate['name-gallery-card-mode']; 
			?>
			<?php if(isset($el)) : ?>
				<?php 
					$el->reset();
					$data = $el->getValue((array) @$rowData);
				?>
				<div class="fabrikDivElement div-name">
					<span class="title name <?php echo $el->getFullName() ?>" title="<?php echo strip_tags($data); ?>">
						<?php
							echo $data;
							$ignoreHeadings[] = $el->getFullName();
						?>
					</span>
				</div>
			<?php endif; ?>
			
			<?php
				$el = $this->elementsToShowOnGridAndCardTemplate['description-gallery-card-mode']; 
			?>
			<?php if(isset($el)) : ?>
				<?php $el->reset(); ?>
				<?php $data = strip_tags($el->getValue((array) @$rowData)); ?>
				<?php if(!empty($data)) : ?>
					<div class="fabrikDivElement div-description">
						<span class="description">	
							<p class="m-0 <?php echo $el->getFullName() ?>">
								<?php
									echo $data;
									$ignoreHeadings[] = $el->getFullName();
								?>
							</p>
						</span>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<!-- Then show all data -->
			<div class="card-data-row d-flex">
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

			<!-- Show created_by and date_time -->
			<div class="fabrikDivElement info-row d-flex align-items-center justify-content-between">
				<div class="div-info d-flex flex-row flex-wrap">
					<?php if (isset($elDate)) : ?>
						<span class="date <?php echo $dateFullName ?>">
							<?php
								$allData = @$rowData;
								echo $elDate->renderListData(@$rowData->$dateRawName, $allData);
							?>
						</span>
					<?php endif; ?>

					<?php if (isset($elOwner)) : ?>
						<span class="owner">
							<span><?php echo Text::_("COM_FABRIK_BY"); ?></span>
							<span class="<?php echo $ownerFullName ?>"><?php echo $elOwner->getValue((array) @$rowData)[0] ?></span>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Finally show fabrik_actions and fabrik_select -->
	<div class="div-actions d-flex align-items-start justify-content-end w-25">
		<span><?php echo @$rowData->fabrik_actions; ?></span>
		<span style="margin-top: 7px;"><?php echo @$rowData->fabrik_select; ?></span>
	</div>
</div>
