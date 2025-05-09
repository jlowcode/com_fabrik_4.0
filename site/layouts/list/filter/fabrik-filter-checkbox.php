<?php
defined('JPATH_BASE') or die;

$d    = $displayData;
$inputDataAttribs = array('data-filter-name="' . $d->elementName . '"');
?>
<div class="fabrikListFilterCheckbox">
<?php
// Process the array to checkboxes limit
$checkboxesLimit = $d->maxCheckboxes;
$allValues = $d->values;
$allLabels = $d->labels;
// Verify if has limit and element number is bigger than limit
if( isset($checkboxesLimit) && 
	!empty($checkboxesLimit) && 
	sizeof($allLabels) > $checkboxesLimit) {
			
	//sidebarCheckboxes div
	echo "<div id='{$d->elementName}_main'>";
	echo "</div>";

	// Add button to see more on the modal
	echo "<a id='{$d->elementName}_modal_button' style='cursor: pointer;' role='button' tabindex='0'>Ver mais... </a>";
	
	// Create modal
	echo "<div data-modal='true' id='{$d->elementName}_modal'>";
		echo "<div id='{$d->elementName}_modal_content'>";
		echo "<div class='seeAllCloseHeader' > <h3>Ver todos</h3> <span class='modalCloseBtnCheckbox' id='{$d->elementName}_modal_button_close'>Fechar X</span> </div>";
		echo "<div style='display: flex;'><h4 style='padding-right: 40px;'>Pesquisar: </h4><input type='text' id='{$d->elementName}_search' /></div>";
		
		echo "<div id='{$d->elementName}_aux'>";
			echo "<div class='checkboxes-container'>";
				echo implode("\n", FabrikHelperHTML::grid($d->values, $d->labels, $d->default, $d->name,
				'checkbox', false, 1, array('input' => array('fabrik_filter')), false, array(), $inputDataAttribs));
			echo "</div>";
		echo "</div>";

		echo "</div>";
	echo "</div>";
	echo " 
	<style>		
		.seeAllCloseHeader {
			display: flex;
			border-botton: 2px;
		}
	
		.modalCloseBtnCheckbox {
			margin-left: auto;
			position: relative;
			right: 0;
			top: 0;
			margin-right: 20px;
			font-size:20px;
			cursor: pointer;
			font-weight: bold;
			z-index: 100;
		}

		#{$d->elementName}_modal {
			display: none;
			position: fixed; 
			z-index: 11;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
			background-color: rgb(0,0,0); 
			background-color: rgba(0,0,0,0.4);
		  }

		  #{$d->elementName}_modal_content {
			overflow: hidden;
			background-color: #fefefe;
			margin: 10% auto;
			padding: 50px;
			border: 1px solid #888;
			width: 80%;
			height: 70%;
			border-radius: 10px 10px 10px 10px;
		  }

		  #{$d->elementName}_modal_content .row-fluid {
			padding-top: 15px;
			padding-bottom: 15px;
			font-size: 20px;
			border-bottom: 0.4px solid rgba(0,0,0,0.4);
		  }		  

		  #{$d->elementName}_aux {
			panding-right: 10px;
			overflow-y: scroll;
			max-height: 60%;
		  }

		  .animate-show{
		   	animation: show 0.8s;
			animation-fill-mode:forwards;
		  }
		  @keyframes show{
			0%{opacity:0}
			100%{opacity:1}
		  }

		  .animate-hide{
		    animation: show 0.8s;
		    animation-fill-mode:forwards;
	      }
	      @keyframes hide{
	  	    0%{opacity:1}
		    100%{opacity:0}
	      }
	</style>
	<script>
		(function () {
			// Create searchBox
			var searchBox = jQuery('#{$d->elementName}_search');
			
			// Get div with all checkboxes
			const auxDiv = jQuery('#{$d->elementName}_aux');
			const mainDiv = jQuery('#{$d->elementName}_main');
			var allCheckboxes = auxDiv.find('.row-fluid');

			// Cut array
			const sideBarCheckboxes = allCheckboxes.toArray().slice(0, $checkboxesLimit);
			const modalCheckboxes = allCheckboxes.toArray().slice($checkboxesLimit, allCheckboxes.length);

			//  Modal code
			const modal = jQuery('#{$d->elementName}_modal');
			const modalContent = jQuery('#{$d->elementName}_modal_content');
			const moreButton = jQuery('#{$d->elementName}_modal_button');
			const closeButton = jQuery('#{$d->elementName}_modal_button_close');

			
			
			// Append first page to sidebar
			mainDiv.append(jQuery(sideBarCheckboxes));

			// Event to trigger search
			searchBox.keyup((event) => {
				const text = event.target.value;
				if(text.length > 2) {
					doSearch(text);
				} else if (text.length == 0) {
					clearSearch();
				}
			});

			function doSearch(text) {
				for(let value of modalCheckboxes) {
					const labelText = jQuery(value).find('span')[0].innerText;
					// Remove accents and set to lower case 
					const labelTextNormalized = labelText.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
					if(labelTextNormalized.search(text.toLowerCase()) !== -1) {
						jQuery(value).css('display', 'block');
					} else {
						jQuery(value).css('display', 'none');
					}
				}
			}

			function clearSearch() {
				for(let value of modalCheckboxes) {
					jQuery(value).css('display', 'block');
				}
			}
			
			moreButton.on('click', () => {
				modal.removeClass('animate-hide');
				modal.addClass('animate-show');
				modal.css('display', 'block');
			});

			closeButton.on('click', () => {
				modal.css('display', 'none');
			});

			window.onclick = function(event) {
				const target = jQuery(event.target);
				if(target.attr('data-modal') && target.attr('data-modal') == 'true') {
					target.removeClass('animate-show');
					target.addClass('animate-hide');
					target.css('display', 'none');
				}
				// if (modal[0] == event.target) {
				// 	modal.addClass('animate-hide');
				// 	modal.css('display', 'none');
				// }
			}
		})();
	</script>
	";
} else {
	echo implode("\n",
				FabrikHelperHTML::grid($d->values, $d->labels, $d->default, $d->name, 'checkbox', false, 1, 
					['input' => ['fabrik_filter', 'form-check-input']], /* Classes */
					false, array(), $inputDataAttribs)
						);
}
?>
</div>