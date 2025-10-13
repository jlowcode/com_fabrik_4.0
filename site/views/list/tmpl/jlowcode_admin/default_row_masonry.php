<?php
/**
 * Fabrik List Template: Div Row
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$rowClass = $this->_row->rowClass ?? '';
$title_element_id = $this->params->get('titulo');
$regexTitle = $title_element_id. '_order';
$ignoreHeadings = ['fabrik_actions', 'fabrik_select'];
$rowData = $this->_row->data;
$elements = $this->getModel()->getElements('filtername');
?>
<div class="masonry-box fabrik_row <?= $rowClass; ?>">
	<div>
		<!-- Show thumb, name and description first -->
		<div class="masonry-head-row">
			<?php $el = $this->elementsToShowOnGridAndCardTemplate['thumb-gallery-card-mode']; ?>
			<?php if(isset($el)) : ?>
				<?php
					$el->reset();
					$thumbFullName = $el->getFullName();
				?>
				<div class="fabrikDivElement">
					<span class="thumb <?= $thumbFullName ?>" >
						<?php
							$thumbRawName = $thumbFullName . '_raw';
							$allData = @$rowData ?? new stdClass();
							$ignoreHeadings[] = $thumbFullName;

							echo $el->renderListData(@$rowData->$thumbRawName, $allData);
						?>
					</span>
				</div>
			<?php endif; ?>

			<?php 
				$el = $this->elementsToShowOnGridAndCardTemplate['name-gallery-card-mode']; 
			?>
			<?php if(isset($el)) : ?>
				<?php 
					$el->reset();
					$data = $el->getValue((array) @$rowData);
				?>
				<div class="fabrikDivElement div-name">
					<span class="title name <?= $el->getFullName() ?>" title="<?= strip_tags($data); ?>">
						<?php
							echo $data;
							$ignoreHeadings[] = $el->getFullName();
						?>
					</span>

                    <span>
                        <div class="fabrik_actions fabrik_element">
                            <?= @$rowData->fabrik_actions; ?>
                        </div>
                    </span>
				</div>
			<?php endif; ?>
			
			<?php $el = $this->elementsToShowOnGridAndCardTemplate['description-gallery-card-mode']; ?>
			<?php if(isset($el) && $el->getParams()->get('show_in_list_summary') == '1') : ?>
				<?php $el->reset(); ?>
				<div class="fabrikDivElement div-description">
					<span class="description">
						<p class="m-0 <?= $el->getFullName() ?>">
							<?php
								echo strip_tags($el->getValue((array) @$rowData));
								$ignoreHeadings[] = $el->getFullName();
							?>
						</p>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<!-- Then show all data -->
		<div class="masonry-data-row">
			<?php foreach ($this->headings as $heading => $label) :
				$d = @$rowData->$heading;
				$showLabel = true;

				// Skip empty elements, id element, created_by element
				if (explode('___', $heading)[1] == "id" || in_array($heading, $ignoreHeadings)) continue;

				// If we use $label from $this->headings the label will be with a tag
				foreach ($elements as $element) {
					if($element->getFullName(true) != $heading) {
						continue;
					}

					if(is_a($element, 'PlgFabrik_ElementYoutube')) {
						$showLabel = false;
					}

					$elModel = $element->getElement();
					$label = $element->getParams()->get('alt_list_heading') ?: $elModel->get('label');
				}

				$h = $this->headingClass[$heading];
				$c = $this->cellClass[$heading];
			?>

				<div class="row-fluid fabrikDivElement fabrikDivElementData">
					<?= $showLabel ? '<span class="title-field-card">' . $label . ': </span>' : ''; ?>
					<?= '<span class="data-field-card ' . $c['class'] . '">' . $d . '</span>'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
