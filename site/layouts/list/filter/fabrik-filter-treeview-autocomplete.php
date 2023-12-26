<?php
defined('JPATH_BASE') or die;

$d    = $displayData;
$inputDataAttribs = array('data-filter-name="' . $d->elementName . '"');
FabrikHelperHTML::iniRequireJS();
$urlbase = JUri::base();
?>
<script src="<?php echo $urlbase; ?>/components/com_fabrik/js//multiSelectTreeViewAutocompleteFilter.js"></script>
<div class="fabrikListFilterCheckbox">
	<div class="autocomplete-treeview-filter">
		<div class="selected-checkbox">
		</div>
		<?php echo $d->data; ?>
		<?php echo ($d->filterBuild == 'count') ? $d->countArray : "" ?>
		<input id="autocomplete-treeview-filter-input-<?php echo $d->elementName; ?>" type="text" autocomplete="off" style="margin-bottom: -15px;">
	</div>
	<div id="tree_<?php echo $d->elementName ?>">
	</div>

</div>